<?php

namespace Plugin\CategoryMetaSeoIngenuity\EventListener;

// use Eccube\Common\EccubeConfig;
use Eccube\Request\Context;
use Eccube\Entity\Category;
use Eccube\Repository\CategoryRepository;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
// use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class CmsiListener implements EventSubscriberInterface
{
    /**
     * @var RequestStack
     */
    // protected $requestStack;

    /**
     * @var EccubeConfig
     */
    // protected $eccubeConfig;

    /**
     * @var Context
     */
    protected $requestContext;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;


    public function __construct(
      // RequestStack $requestStack,
      // EccubeConfig $eccubeConfig,
      Context $requestContext,
      CategoryRepository $categoryRepository
      )
    {
        // $this->requestStack = $requestStack;
        // $this->eccubeConfig = $eccubeConfig;
        $this->requestContext = $requestContext;
        $this->categoryRepository = $categoryRepository;
    }

    public function onKernelRequest(FilterResponseEvent $event)
    {
      if (!$event->isMasterRequest()) {
          return;
      }
      if ($this->requestContext->isFront()) {
        $request = $event->getRequest();
        $pathInfo = $request->getPathInfo();
        if( strpos($pathInfo,'/products/list') === false || $request->query->get('category_id') == "" ){
          return;
        }
        log_info('[CMS]category_id', [$request->query->get('category_id')]);

        $response = $event->getResponse();
        $content = $response->getContent();
        $Category = $this->categoryRepository->find( $request->query->get('category_id') );
        log_info('[CMS]$Category', [ $Category ]);

        $title = $Category->getCseoTitle();
        if( $title !== null ){
          $title = "<title>{$title}</title>";
          preg_match('/\<title\>(.*?)\<\/title\>/s', $content, $matches_title);
          if( $matches_title != false){
            $content = str_replace( $matches_title[0] , $title, $content);
          }else{
            $content = str_replace( "</head>" , $title."\r\n</head>" , $content);
          }
        }

        $description = $Category->getCseoDescription();
        if( $description !== null ){
          $description = "<meta name=\"description\" content=\"{$description}\" >";
          preg_match('/\<meta name=\"description\" (.*?)\>/s', $content, $matches_description);

          if( $matches_description != false){
            $content = str_replace( $matches_description[0] , $description, $content);
          }else{
            $content = str_replace( "</head>" , $description."\r\n</head>", $content);
          }
        }


        $robots = $Category->getCseoRobots();
        if( $robots !== null ){
          $robots = "<meta name=\"robots\" content=\"{$robots}\" >";
          preg_match('/\<meta name=\"robots\" (.*?)\>/', $content, $matches_robots);

          if( $matches_robots != false){
            $content = str_replace( $matches_robots[0] , $robots, $content);
          }else{
            $content = str_replace( "</head>" , $robots."\r\n</head>", $content);
          }
        }

        $response->setContent($content);

      }
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.response' => ['onKernelRequest', 512],
        ];
    }

}
