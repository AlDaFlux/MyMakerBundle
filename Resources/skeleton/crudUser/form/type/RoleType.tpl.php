<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class <?= $class_name ?> extends AbstractType
{
    private $roleHierarchy;
    private $parametesr;
    private $roles;

    public function __construct(RoleHierarchyInterface $roleHierarchy, ParameterBagInterface $parameters)
    {
        $this->roleHierarchy = $roleHierarchy;
        $this->parameters = $parameters;
       
        $this->roles = array();
        foreach ($this->parameters->Get('security.role_hierarchy.roles') as $key => $value) {
            $this->roles["user.roles.".strtolower($key)] = $key;
            foreach ($value as $value2) {
                $this->roles["user.roles.".strtolower($value2)] = $value2;
            }
        }
        $this->roles = array_unique($this->roles);        
        
        if (! $this->roles)
        {
         $this->roles = [
            'user.role.role_user' => 'ROLE_USER',
            'user.role.role_admin' => 'ROLE_ADMIN'
            ];
        }
               
    }
    
     
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
         $builder->add('roles', ChoiceType::class, [
                'choices' => $this->roles,
                'expanded' => true,
                'multiple' => true,
                'label' => 'user.role.plurial' 
            ]);
    }
}
