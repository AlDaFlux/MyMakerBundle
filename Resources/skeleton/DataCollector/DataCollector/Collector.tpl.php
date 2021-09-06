<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


use <?= $extend_class; ?>;


class <?= $class_name ?> extends <?= $parent_class_name; ?>
{

    public function __construct()
    {
    
    }
    
    
    public function getName() : string
    {
        return '<?= $collector_name_string ?>';
    }
    
    
     public function reset(): void
    {
        $this->data = [];
    }

    
    
//    Response $response
            
    public function collect(Request $request, Response $response, \Throwable $exception = null)
    {
            $this->data = ['test' => "OK"];
    }
    
    

    public static function getTemplate(): ?string
    {
        return '<?= $template_path ?>';
    }
    
    
    
 
    
    
    
    
}