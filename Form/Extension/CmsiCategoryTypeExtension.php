<?php

namespace Plugin\CategoryMetaSeoIngenuity42\Form\Extension;

use Eccube\Form\Type\Admin\CategoryType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints as Assert;

class CmsiCategoryTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('cseo_title', TextType::class, [
                'label' => '[SEO]タイトル',
                'required' => false,
                'eccube_form_options' => [
                    'auto_render' => true,
                ],
                'constraints' => [
                    new Assert\Length(['max' => 60]),
                ],
        ]);
        $builder
            ->add('cseo_description', TextareaType::class, [
                'label' => '[SEO]ディスクリプション',
                'required' => false,
                'eccube_form_options' => [
                    'auto_render' => true,
                ],
                'constraints' => [
                    new Assert\Length(['max' => 320]),
                ],
        ]);
        
        $builder
            ->add('cseo_robots', ChoiceType::class, [
                'label' => '[SEO]検索エンジンにインデックス',
                'choices' => [
                    'する(index,follow)' => "index,follow",
                    'しない(noindex,nofollow)' => "noindex,nofollow",
                ],
                'eccube_form_options' => [
                    'auto_render' => true,
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                ],
        ]);

    }

    public function getExtendedType()
    {
        return CategoryType::class;
    }

    /**
    * Return the class of the type being extended.
    */
    public static function getExtendedTypes(): iterable
    {
        yield CategoryType::class;
    }

}
