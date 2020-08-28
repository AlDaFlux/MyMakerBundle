<?php
namespace App\Maker;


use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;


use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Common\Inflector\Inflector as LegacyInflector;
use Doctrine\Inflector\InflectorFactory;
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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Validator\Validation;

/**
 * @author Sadicov Vladimir <sadikoff@gmail.com>
 */
final class MakeBundleTest extends AbstractMaker
{

    private $vendor;
    private $bundleName;

    public function __construct()
    {
         
        if (class_exists(InflectorFactory::class)) {
            $this->inflector = InflectorFactory::create()->build();
        }
    }
 
    
    public static function getCommandName(): string
    {
        return 'make:bundle:test';
    }

    /**
     * {@inheritdoc}
     */
    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->setDescription('Creates Bundle test')
            ->setHelp("Create an empty bundle")
            ->addArgument('vendor', InputArgument::OPTIONAL, sprintf('The class name of the vendor (e.g. <fg=yellow>%s</>)', Str::asClassName(Str::getRandomTerm()."")))
            ->addArgument('bundle-name', InputArgument::OPTIONAL, sprintf('The class name of the bundle to create (e.g. <fg=yellow>%s</>)', Str::asClassName(Str::getRandomTerm()."Bundle")))
        ;
        /*
         
         * 
         *  
        $inputConfig->setArgumentAsNonInteractive('vendor');
        $inputConfig->setArgumentAsNonInteractive('bundleNamewithSuffixe');
         * 
         */

    }
 
/*    
    public function interact(InputInterface $input, ConsoleStyle $io, Command $command)
    {
        if (null === $input->getArgument('entity-class')) {
            $argument = $command->getDefinition()->getArgument('entity-class');
            $question = new Question($argument->getDescription());
            $value = $io->askQuestion($question);
            $input->setArgument('entity-class', $value);
        }
    }
*/
    
    
    public function getSkeletonFolder()
    {
        return("src/Resources/skeleton/bundle/");
    }

    public function getBundleRootFolder()
    {
        return(strtolower($this->getVendorName())."/".Str::asCommand($this->getBundleName()));
    }
    
    public function getVendorName()
    {
        return(Str::asCamelCase(strtolower($this->getVendorOriginalName())));
    }
    
    
    public function getVendorOriginalName()
    {
        return($this->vendor);
    }
    
    
    public function getBundleName()
    {
        return($this->bundleName);
    }
    
    public function getBundleNameSimple()
    {
        return(Str::removeSuffix($this->getBundleName(),"Bundle"));
    }
    
    
    public function getAuthorInfos()
    {
        return (["name"=>"Antoine LOTZ","email"=>"antoine.lotz@gmail.com","site"=>"https://github.com/".$this->getVendorName()]);
//        return("ChartjsBundle");
    }
    
    public function getHomepage()
    {
         return("https://github.com/".$this->getVendorName()."/".$this->getBundleName());
    }
    
    
    

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
  
            $this->vendor=$input->getArgument('vendor');
            $this->bundleName=$input->getArgument('bundle-name');

            /*
        $varTest="AlDaFlux";
        $varTest=$this->getVendorName();
        $varTest=$this->getBundleName();
        
    
        $varTest=Str::removeSuffix($this->getVendorName().'/'.$this->getBundleName(),"Bundle");
        dump(Str::asHumanWords($varTest));
        dump(Str::asFilePath($varTest));
        dump(Str::getNamespace($varTest));
        dump(Str::asRoutePath($varTest));
        dump("ICI");
        dump(Str::asRouteName($varTest));
        dump("ICI");
        dump(Str::asCommand($varTest));
        dump(Str::asSnakeCase($varTest));
        dump(Str::asCamelCase($varTest));
        dump(Str::asLowerCamelCase($varTest));
        dump(Str::asTwigVariable($varTest));

        
        dump(Str::getNamespace($varTest));
  
        return(0);
        /*
        */
        $bundleClassNameDetails = $generator->createClassNameDetails('\\'.$this->getVendorName().'\\'.$this->getBundleName(), 'Controller', 'Controller');

        $nameSpace= ($bundleClassNameDetails->getFullName());
        $className=$this->getBundleName();
 
        $variables=[
                'class_name' => Str::getShortClassName($className),
                'namespace' => $nameSpace,
            ];
         
        $generator->generateFile($this->getBundleRootFolder()."/".$this->getBundleName().'.php',$this->getSkeletonFolder().'bundle.tpl.php',$variables);
 

        $variables=[
                'name' => strtolower( $this->getVendorName().'/'.Str::asCommand($this->getBundleName())),
                'namespace' => $nameSpace,
                'homepage' => $this->getHomepage(),
                'author' => $this->getAuthorInfos(),
                'psr4' => $this->getVendorName().'\\\\'.$this->getBundleName()."\\\\",
            ];
        $generator->generateFile($this->getBundleRootFolder()."/composer.json",$this->getSkeletonFolder().'composer.tpl.json',$variables);

        // README.md
        $generator->generateFile($this->getBundleRootFolder()."/README.md",$this->getSkeletonFolder().'README.tpl.md',["name"=>$this->getBundleName()]);



        $rootNode=Str::asRouteName($this->getVendorName().'/'.$this->getBundleNameSimple());
        $generator->generateFile($this->getBundleRootFolder()."/DependencyInjection/Configuration.php",$this->getSkeletonFolder().'/DependencyInjection/Configuration.tpl.php',['namespace' => $nameSpace,'root_node' => $rootNode]);


        $generator->generateFile($this->getBundleRootFolder()."/DependencyInjection/".$this->getBundleNameSimple()."Extension.php",$this->getSkeletonFolder().'/DependencyInjection/Extension.tpl.php',['namespace' => $nameSpace,'bundle_name_simple' => $this->getBundleNameSimple(),'root_node' => $rootNode]);

        
        $generator->writeChanges();
        $this->writeSuccessMessage($io);


                
        $io->text([
            'Next: Open your voter and add your logic.',
            'Find the documentation at <fg=yellow>https://symfony.com/doc/current/security/voters.html</>',
        ]);
          
        $io->text(sprintf('Next: Check your new CRUD by going to <fg=yellow>%s/</>', "321321"));
    }

    
    
    /**
     * {@inheritdoc}
     */
    public function configureDependencies(DependencyBuilder $dependencies)
    {
        $dependencies->addClassDependency(
            Route::class,
            'router'
        );
  
        $dependencies->addClassDependency(
            TwigBundle::class,
            'twig-bundle'
        );
  
        $dependencies->addClassDependency(
            ParamConverter::class,
            'annotations'
        );
    }

     
}
