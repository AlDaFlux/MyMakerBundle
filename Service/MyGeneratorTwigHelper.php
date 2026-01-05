<?php

namespace Aldaflux\MyMakerBundle\Service;

use Symfony\Bundle\MakerBundle\FileManager;

 

final class MyGeneratorTwigHelper
{
    
    public function __construct(
        private FileManager $fileManager,
    ) {
    }

    public function getTdFormVar($entity, $field, $route_name =null, $entity_identifier=null, $entity_twig_var_singular=null): string
    {
        $twigField = preg_replace_callback('/(?!^)_([a-z0-9])/', static fn ($s) => strtoupper($s[1]), $field['fieldName']);
        $printCode = $entity.'.'.str_replace('_', '', $twigField);


            /*
             *                     <td class='center'>
                        {% if <?= $entity_twig_var_singular ?>.<?=  $field["fieldName"] ?> %}
                            <i class='{% trans %}crud.boolean.true_classe{% endtrans %}'> 
                        {% else %}
                            <i class='{% trans %}crud.boolean.false_classe{% endtrans %}'>
                        {% endif  %}
                    </td>
             */


        switch ($field['type'])
        {
            case 'datetimetz_immutable': 
            case 'datetimetz' :
                    $printCode .= ' ? '.$printCode.'|date(\'d-m-Y H:i:s T\') : \'\'';            
                break;
            case 'datetime_immutable': 
            case 'datetime' :
                    $printCode .= $printCode .= ' ? '.$printCode.'|date(\'d-m-Y H:i:s\') : \'\'';            
                break;
            case 'dateinterval': 
                    $printCode .= $printCode .= ' ? '.$printCode.'|date(\'d-m-Y H:i:s\') : \'\'';            
                break;
            case 'dateinterval':
                    $printCode .= ' ? '.$printCode.'.format(\'%y year(s), %m month(s), %d day(s)\') : \'\'';
                break;
            case 'date_immutable':
            case 'date':
                    $printCode .= ' ? '.$printCode.'|date(\'d-m-Y\') : \'\'';
                break;
            
            case 'time_immutable':
            case 'time':
                    $printCode .= ' ? '.$printCode.'|date(\'H:i:s\') : \'\'';
                break;
            case 'json':
                    $printCode .= ' ? '.$printCode.'|json_encode : \'\'';
                break;
            case 'array':
                    $printCode .= ' ? '.$printCode.'|join(\', \') : \'\'';
                break;
            case 'boolean':
                    $printCode="
                    {% if ".$printCode." %}
                        <i class='{% trans %}crud.boolean.true_classe{% endtrans %}'> 
                    {% else %}
                        <i class='{% trans %}crud.boolean.false_classe{% endtrans %}'>
                    {% endif  %}";
                break;
        }


        if ($field['type']!='boolean')
        {
            $printCode="{{".$printCode."}}";
        }
        

        if ($route_name)
        {
            return "<td> <a href='{{path('".$route_name."',{'".$entity_identifier."':".$entity_twig_var_singular.".".$entity_identifier."})}}'>".$printCode."</td>";
        }
        else
        {
            return "<td>".$printCode."</td>";
        }
    }

    /*
    public function getHeadPrintCode($title): string
    {
        if ($this->fileManager->fileExists($this->fileManager->getPathForTemplate('base.html.twig'))) {
            return <<<TWIG
                {% extends 'base.html.twig' %}

                {% block title %}$title{% endblock %}

                TWIG;
        }

        return <<<HTML
            <!DOCTYPE html>

            <title>$title</title>

            HTML;
    }
     * 
     */

    
}
