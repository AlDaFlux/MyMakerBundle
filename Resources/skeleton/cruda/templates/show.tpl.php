<?= $helper->getHeadPrintCode($entity_class_name) ?>

{% block body %}
    <h1><a href="{{ path('<?= $route_name ?>_index') }}"><?= $human_word_entity_name_plural ?></a></h1>

    <table class="table table-bordered">
        <tbody>
<?php foreach ($entity_fields as $field): ?>
            <tr>
                <th><?= ucfirst($fields_legend[$field['fieldName']]) ?></th>
                <?= $my_helper->getTdFormVar($entity_twig_var_singular, $field) ?>
            </tr>
<?php endforeach; ?>
        </tbody>
    </table>
 

    <div class='row'>
        <?php if ($with_voter) {?>{% if is_granted('edit', <?= $entity_twig_var_singular ?>) %}<?php }?><div class='col'><a class='btn-edit' href="{{ path('<?= $route_name ?>_edit', {'<?= $entity_identifier ?>': <?= $entity_twig_var_singular ?>.<?= $entity_identifier ?>}) }}">{% trans %}crud.edit.text{% endtrans %}</a></div><?php if ($with_voter) {?>{% endif  %}<?php }?>
        
    <?php if ($with_voter) {?>
    {% if is_granted('delete', <?= $entity_twig_var_singular ?>) %}
        <?php }?>
        <div class='col'>{{ include('<?= $templates_path ?>/_delete_form.html.twig') }}</div>
        <?php if ($with_voter) {?>
{% endif  %}<?php }?> 
    </div>
            
    
{% endblock %}
