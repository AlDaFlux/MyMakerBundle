<?php

namespace Aldaflux\MyMakerBundle\Maker;

 
use Aldaflux\MyMakerBundle\Maker\MakeAdmin;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Console\Input\InputOption;

use Symfony\Component\Security\Core\User\UserInterface;
 

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Common\Inflector\Inflector as LegacyInflector;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Renderer\FormTypeRenderer;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Validator\Validation;

use Doctrine\Common\Annotations\AnnotationReader;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface; 


/**
 * @author Sadicov Vladimir <sadikoff@gmail.com>
 */
final class MakeCrudUser extends MakeAdmin
{

    public static function getCommandName(): string
    {
        return 'make:crud:user';
    }

    public function __construct(DoctrineHelper $doctrineHelper, FormTypeRenderer $formTypeRenderer, ParameterBagInterface $parameters)
    {
        parent::__construct($doctrineHelper,$formTypeRenderer,$parameters);
    }

   

    /**
     * {@inheritdoc}
     */
    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->setDescription('Creates CRUD for User  entity class')
            ->addOption('with-voter', null, InputOption::VALUE_NONE, "Créer les boutons d'accès avec les voter")
            ->setHelp("file_get_contents(__DIR__.'/../Resources/help/MakeCrud.txt'");
        $inputConfig->setArgumentAsNonInteractive('entity-class');
    }


    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $withVoter = $input->getOption('with-voter');
        

        $templatePathDir = __DIR__.'/../Resources/skeleton/';

        if (class_exists('App\\Entity\\User') && isset(class_implements('App\\Entity\\User')[UserInterface::class])) 
        {
            $entityUserClass = 'App\\Entity\\User';
        }
        else
        {
              $io->newLine();
                $io->writeln(' <bg=red;fg=white>          </>');
                $io->writeln(' <bg=red;fg=white> erreur ! </>');
                $io->writeln(' <bg=red;fg=white> App\\Entity\\User not found </>');
                $io->writeln(' <bg=red;fg=white>          </>');
                $io->newLine();
             return (1);
        } 

        $entityClassDetails = $generator->createClassNameDetails(
            Validator::entityExists("User", $this->doctrineHelper->getEntitiesForAutocomplete()),
            'Entity\\'
        );

          
        $entityDoctrineDetails = $this->doctrineHelper->createDoctrineDetails($entityClassDetails->getFullName());

        $repositoryVars = [];

        if (null !== $entityDoctrineDetails->getRepositoryClass()) {
            $repositoryClassDetails = $generator->createClassNameDetails(
                '\\' . $entityDoctrineDetails->getRepositoryClass(),
                'Repository\\',
                'Repository'
            );

            $repositoryVars = [
                'repository_full_class_name' => $repositoryClassDetails->getFullName(),
                'repository_class_name' => $repositoryClassDetails->getShortName(),
                'repository_var' => lcfirst($this->singularize($repositoryClassDetails->getShortName())),
            ];
        }

        $controllerClassDetails = $generator->createClassNameDetails(
            $entityClassDetails->getRelativeNameWithoutSuffix() . 'Controller',
            'Controller\\' . $this->folderControllerName,
            'Controller'
        );

        $iter = 0;
        do {
            $formClassDetails = $generator->createClassNameDetails(
                $entityClassDetails->getRelativeNameWithoutSuffix() . ($iter ?: '') . 'Type',
                'Form\\',
                'Type'
            );
            ++$iter;
        } while (class_exists($formClassDetails->getFullName()));
        
        
        
        
        
        
        
        $reflect = new \ReflectionClass($entityClassDetails->getFullName());
        $entityFulName = $entityClassDetails->getFullName();
        $props = $reflect->getProperties();
        $propertiesList = [];
        foreach ($props as $prop) {
            $propertiesList[] = $prop->getName();
        }
        $propertiesList = array_diff($propertiesList, ['id']);
        
        $email=in_array("email", $propertiesList);
        $username=in_array("username", $propertiesList);
        
 
        $fieldsNotInForm = ['updatedAt', 'createdAt', 'updatedBy', 'createdBy', 'roles', 'password'];
        $fieldsNotDisplay = ['password'];
        foreach ($entityDoctrineDetails->getDisplayFields() as $fieldToDisplay) {
            if (!in_array($fieldToDisplay["fieldName"], $fieldsNotDisplay)) {
                $displayFileds[] = $fieldToDisplay;
            }
        }


        foreach ($propertiesList as $field) {
            if (!in_array($field, $fieldsNotInForm)) 
            {
                $fieldType = $this->getFieldType($entityFulName, $field);
                if ($fieldType == "date") {
                    $formFields[$field] = ['type' => "DateType::class", 'options_code' => "'widget' => 'single_text'"];
                }
                elseif ($field == "role") {
                    $formFields[$field] = ['type' => "RoleType::class", 'options_code' => "'label' => 'false'"];
                } else {
                    $formFields[$field] = ['type' => null, 'options_code' => null];
                }
            }
        }
        
        
        
        
        
        
         
        
            $variables = ['entity_full_class_name' => $entityUserClass, 'form_fields'=>$formFields];
          
            $generator->generateClass(
            $formClassDetails->getFullName(),
            $templatePathDir."crudUser/form/UserType.tpl.php",
            $variables
            );

            
          
       
        $formTypesNamesSimple=["Role","RepeatedPassword","ChangePassword"];
        $formTypes=array();
        foreach ($formTypesNamesSimple as $formTypeNamesSimple)
        {
            $iter = 0;
            do {
                $formTypeClassDetails = $generator->createClassNameDetails(
                    $formTypeNamesSimple. ($iter ?: '') . 'Type',
                    'Form\\Type\\',
                    'Type'
                );
                ++$iter;
            } while (class_exists($formTypeClassDetails->getFullName()));
            $formTypes[$formTypeNamesSimple]=$formTypeClassDetails;
            
            
        }
    
        $formTypesFullNames=array();
        
        foreach ($formTypes as $name => $classe)
        {
            $generator->generateClass(
            $classe->getFullName(),
            $templatePathDir."crudUser/form/type/".$name."Type.tpl.php" 
            );
            
            $formTypesFullNames[]=$classe->getFullName();
            
        }
            
      
            
        
            
        

        $entityVarPlural = lcfirst($this->pluralize($entityClassDetails->getShortName()));
        $entityVarSingular = lcfirst($this->singularize($entityClassDetails->getShortName()));

        $entityTwigVarPlural = Str::asTwigVariable($entityVarPlural);
        $entityTwigVarSingular = Str::asTwigVariable($entityVarSingular);


        $routeName = $this->routeNamePrefix . Str::asRouteName($controllerClassDetails->getRelativeNameWithoutSuffix());
        $templatesPath = $this->templateFolder . Str::asFilePath($controllerClassDetails->getRelativeNameWithoutSuffix());

        $route_path = $this->routePathPrefix . Str::asRoutePath($controllerClassDetails->getRelativeNameWithoutSuffix());

                
 
        $generator->generateController(
            $controllerClassDetails->getFullName(),
            $templatePathDir."crudUser/controller/Controller.tpl.php",
            array_merge(
                [
                    'entity_full_class_name' => $entityClassDetails->getFullName(),
                    'entity_class_name' => $entityClassDetails->getShortName(),
                    'form_full_class_name' => $formClassDetails->getFullName(),
                    'form_class_name' => $formClassDetails->getShortName(),
                    'route_path' => $route_path,
                    'route_name' => $routeName,
                    'templates_path' => $templatesPath,
                    'entity_var_plural' => $entityVarPlural,
                    'entity_twig_var_plural' => $entityTwigVarPlural,
                    'entity_var_singular' => $entityVarSingular,
                    'entity_twig_var_singular' => $entityTwigVarSingular,
                    'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                    'with_voter' => $withVoter, 
                    'username' => $username, 
                    'email' => $email, 
                    'form_types_full_names' => $formTypesFullNames, 
                    

                ],
                $repositoryVars
            )
        );
      
          
        
       

        //        $formFields= array_diff($formFields,$fieldsNotInForm);

 
        $templates = [
            '_delete_form' => [
                'route_name' => $routeName,
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                'with_voter' => $withVoter,
            ],
            '_form' => [],
            'edit' => [
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                'route_name' => $routeName,
                'templates_path' => $templatesPath,
                'with_voter' => $withVoter,
                'extend' => $this->extend,
            ],
            'change_password' => [
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                'route_name' => $routeName,
                'templates_path' => $templatesPath,
                'with_voter' => $withVoter,
                'extend' => $this->extend,
            ],
            'index' => [
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_twig_var_plural' => $entityTwigVarPlural,
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                'entity_fields' => $displayFileds,
                'route_name' => $routeName,
                'with_voter' => $withVoter,
                'extend' => $this->extend,
            ],
            'new' => [
                'entity_class_name' => $entityClassDetails->getShortName(),
                'route_name' => $routeName,
                'templates_path' => $templatesPath,
                'extend' => $this->extend,
            ],
            'show' => [
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                'entity_fields' => $displayFileds,
                'route_name' => $routeName,
                'templates_path' => $templatesPath,
                'with_voter' => $withVoter,
                'extend' => $this->extend,
            ],
            'profile' => [
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                'entity_fields' => $displayFileds,
                'route_name' => $routeName,
                'templates_path' => $templatesPath,
                'with_voter' => $withVoter,
                'extend' => $this->extend,
            ],
        ];
        

        
        
        foreach ($templates as $template => $variables) {
            $generator->generateTemplate(
                $templatesPath . '/' . $template . '.html.twig',
                $templatePathDir.'crudUser/templates/'.$template.'.tpl.php',
                $variables
            );
        }

        $generator->writeChanges();

        $this->writeSuccessMessage($io);

        $io->text(sprintf('Next: Check your new CRUD by going to <fg=yellow>%s/</>', $route_path));
    }

    
}
