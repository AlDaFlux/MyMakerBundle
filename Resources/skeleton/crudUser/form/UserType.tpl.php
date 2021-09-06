<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use <?= $entity_full_class_name ?>;

use App\Form\Type\RepeatedPasswordType;
use App\Form\Type\RoleType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class <?= $class_name ?> extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options["mode"]=="new" or $options["mode"]=="edit")
        {
            $builder
                <?php foreach ($form_fields as $form_field => $typeOptions): ?>
                <?php if (null === $typeOptions['type'] && !$typeOptions['options_code']): ?>
    ->add('<?= $form_field ?>')
                <?php elseif (null !== $typeOptions['type'] && !$typeOptions['options_code']): ?>
    ->add('<?= $form_field ?>', <?= $typeOptions['type'] ?>::class)
                <?php else: ?>
    ->add('<?= $form_field ?>', <?= $typeOptions['type'] ? ($typeOptions['type'].'::class') : 'null' ?>, [<?= $typeOptions['options_code'] ?>])
                <?php endif; ?>
                <?php endforeach; ?>
            ;
            
        }
        if ($options["mode"]=="new" or $options["mode"]=="edit_password")
        {
            $builder->add('newPassword', RepeatedPasswordType::class, ['mapped' => false,"label"=>false]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'mode' => null,
            'allow_extra_fields' => true,
        ]);
    }
}
