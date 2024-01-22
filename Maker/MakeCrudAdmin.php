<?php

namespace Aldaflux\MyMakerBundle\Maker;

use Aldaflux\MyMakerBundle\Maker\MakeAdmin;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Console\Input\InputOption;

 
use Symfony\Bundle\MakerBundle\ConsoleStyle;

use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Renderer\FormTypeRenderer;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\Question;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface; 


/**
 * @author Sadicov Vladimir <sadikoff@gmail.com>
 */
final class MakeCrudAdmin extends MakeAdmin
{

    public static function getCommandName(): string
    {
        return 'make:crud:admin';
    }
    
    public static function getCommandDescription(): string
    {
        return 'make:crud:admin';
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
            ->setDescription('Creates CRUD for Doctrine entity class')
            ->addArgument('entity-class', InputArgument::OPTIONAL, sprintf('The class name of the entity to create CRUD (e.g. <fg=yellow>%s</>)', Str::asClassName(Str::getRandomTerm())))
            ->addOption('with-voter', null, InputOption::VALUE_NONE, "Créer les boutons d'accès avec les voter")
            ->setHelp("file_get_contents(__DIR__.'/../Resources/help/MakeCrud.txt'");
        $inputConfig->setArgumentAsNonInteractive('entity-class');
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command) : void
    {
        if (null === $input->getArgument('entity-class')) {
            $argument = $command->getDefinition()->getArgument('entity-class');

            $entities = $this->doctrineHelper->getEntitiesForAutocomplete();

            $question = new Question($argument->getDescription());
            $question->setAutocompleterValues($entities);

            $value = $io->askQuestion($question);

            $input->setArgument('entity-class', $value);
        }
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $withVoter = $input->getOption('with-voter');



        $entityClassDetails = $generator->createClassNameDetails(
            Validator::entityExists($input->getArgument('entity-class'), $this->doctrineHelper->getEntitiesForAutocomplete()),
            'Entity\\'
        );
        
        $humanWordEntityName=Str::asHumanWords($input->getArgument('entity-class'));
        
        
        
        
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


        $entityVarPlural = lcfirst($this->pluralize($entityClassDetails->getShortName()));
        $entityVarSingular = lcfirst($this->singularize($entityClassDetails->getShortName()));

        $entityTwigVarPlural = Str::asTwigVariable($entityVarPlural);
        $entityTwigVarSingular = Str::asTwigVariable($entityVarSingular);


        $routeName = $this->routeNamePrefix . Str::asRouteName($controllerClassDetails->getRelativeNameWithoutSuffix());
        $templatesPath = $this->templateFolder . Str::asFilePath($controllerClassDetails->getRelativeNameWithoutSuffix());

        $route_path = $this->routePathPrefix . Str::asRoutePath($controllerClassDetails->getRelativeNameWithoutSuffix());

                
        $templatePathDir = __DIR__.'/../Resources/skeleton/';

                
        $generator->generateController(
            $controllerClassDetails->getFullName(),
            $templatePathDir."crudAdmin/controller/Controller.tpl.php",
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
                    'with_voter' => $withVoter

                ],
                $repositoryVars
            )
        );


        $constraintClasses = [];
        $extraUseClasses = [];

        $reflect = new \ReflectionClass($entityClassDetails->getFullName());

        $entityFulName = $entityClassDetails->getFullName();

        $props = $reflect->getProperties();
        $propertiesList = [];
        foreach ($props as $prop) {
            $propertiesList[] = $prop->getName();
        }
        $propertiesList = array_diff($propertiesList, ['id']);


        $fieldsWithTypes = [];


        $fieldsNotInForm = ['updatedAt', 'createdAt', 'updatedBy', 'createdBy'];
        $fieldsNotDisplay = ['password'];

        foreach ($entityDoctrineDetails->getDisplayFields() as $fieldToDisplay) {
            if (!in_array($fieldToDisplay["fieldName"], $fieldsNotDisplay)) {
                $displayFileds[] = $fieldToDisplay;
            }
        }


        foreach ($propertiesList as $field) {
            if (!in_array($field, $fieldsNotInForm)) {
                $fieldType = $this->getFieldType($entityFulName, $field);
                if ($fieldType == "date") {
                    $formFields[$field] = ['type' => DateType::class, 'options_code' => "'widget' => 'single_text'",];
                } else {
                    $formFields[$field] = null;
                }
            }
        }

        //        $formFields= array_diff($formFields,$fieldsNotInForm);


        $this->formTypeRenderer->render(
            $formClassDetails,
            $formFields,
            $entityClassDetails,
            $constraintClasses,
            $extraUseClasses
        );

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
                'human_word_entity_name' => $humanWordEntityName,
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
                'human_word_entity_name' => $humanWordEntityName,
            ],
            'new' => [
                'entity_class_name' => $entityClassDetails->getShortName(),
                'route_name' => $routeName,
                'templates_path' => $templatesPath,
                'extend' => $this->extend,
                'human_word_entity_name' => $humanWordEntityName,
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
                'human_word_entity_name' => $humanWordEntityName,
                
            ],
        ];


        //dump($templates["index"]);

        foreach ($templates as $template => $variables) {
            $generator->generateTemplate(
                $templatesPath . '/' . $template . '.html.twig',
                $templatePathDir.'crudAdmin/templates/'.$template.'.tpl.php',
                $variables
            );
        }

        $generator->writeChanges();

        $this->writeSuccessMessage($io);

        $io->text(sprintf('Next: Check your new CRUD by going to <fg=yellow>%s/</>', $route_path));
    }
 
}
