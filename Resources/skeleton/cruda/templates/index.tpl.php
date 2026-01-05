<?= $helper->getHeadPrintCode($entity_class_name.' index'); ?>

{% block body %}
    <h1><a href="{{ path('<?= $route_name ?>_index') }}"><?= $human_word_entity_name_plural ?></a></h1>
    
    
    <table class="table table-bordered table-triable">
        <thead>
            <tr>
<?php foreach ($entity_fields as $field): ?>
                <th><?= ucfirst($fields_legend[$field['fieldName']]) ?></th>
                
<?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            
<?php $first=true ?> 
        {% for <?= $entity_twig_var_singular ?> in <?= $entity_twig_var_plural ?> %}
            <tr>
<?php foreach ($entity_fields as $field): ?>
    
                        <?php if ($first) {?>
                            <?= $my_helper->getTdFormVar($entity_twig_var_singular, $field, $route_name."_show", $entity_identifier , $entity_twig_var_singular ) ?>
                        <?php } else { ?> 
                        <?php $first=false; ?> 
                                <?= $my_helper->getTdFormVar($entity_twig_var_singular, $field) ?>
                        <?php } ?> 
                        <?php $first=false; ?> 
<?php endforeach; ?>
            </tr>
        {% else %}
            <tr>
                <td colspan="<?= (count($entity_fields) + 1) ?>">{% trans %}crud.list.none{% endtrans %}</td>
            </tr>
        {% endfor %}
        </tbody>
    </table>

    
    
    <?php if ($with_voter) {?>
        {% if is_granted('<?= $voter_create ?>') %}
    <?php }?>
        <a class='btn-new' href="{{ path('<?= $route_name ?>_new') }}"> <i class='{% trans %}crud.new.icon{% endtrans %}'></i> {% trans %}crud.new.text{% endtrans %}  <?= $human_word_entity_name_singular ?> </a>
    <?php if ($with_voter) {?>
        {% endif %}
    <?php }?>

    
    
{% endblock %}
