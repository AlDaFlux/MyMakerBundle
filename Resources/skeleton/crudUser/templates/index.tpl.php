<?php if ($extend) {?>
    {% extends "<?= $extend ?>" %}
<?php }?>
    



{% block body %}
    <h1><a href="{{ path('<?= $route_name ?>_index') }}"><?= $entity_class_name ?></a></h1>

    <table class="table table-bordered table-triable">
        <thead>
            <tr>
<?php foreach ($entity_fields as $field): ?>
                <th><?= ucfirst($field['fieldName']) ?></th>
<?php endforeach; ?>
                <th>actions</th>
            </tr>
        </thead>
        <tbody>
        {% for <?= $entity_twig_var_singular ?> in <?= $entity_twig_var_plural ?> %}
            <tr>
<?php foreach ($entity_fields as $field): ?>
                
                <?php if ($field["type"]=="boolean") { ?>
                    <td class='center'>
                        {% if <?= $entity_twig_var_singular ?>.<?=  $field["fieldName"] ?> %}
                            <i class='boolean boolean-true glyphicon glyphicon-ok'>
                        {% else %}
                            <i class='boolean boolean-false glyphicon glyphicon-remove'>
                        {% endif  %}
                    </td>
                <?php } else { ?>
                    <td>{{ <?= $helper->getEntityFieldPrintCode($entity_twig_var_singular, $field) ?> }}</td>
                <?php } ?>

<?php endforeach; ?>
                <td>
                    
                    <a class='btn btn-primary'  href="{{ path('<?= $route_name ?>_show', {'<?= $entity_identifier ?>': <?= $entity_twig_var_singular ?>.<?= $entity_identifier ?>}) }}">
                        <i class='{% trans %}crud.show.icon{% endtrans %}'></i>
                        {% trans %}crud.show.text{% endtrans %}

                    </a>
                    

                    <?php if ($with_voter) {?>
                        {% if is_granted('edit', <?= $entity_twig_var_singular ?>) %}
                    <?php }?>
                    <a class='btn btn-warning'   href="{{ path('<?= $route_name ?>_edit', {'<?= $entity_identifier ?>': <?= $entity_twig_var_singular ?>.<?= $entity_identifier ?>}) }}">
                        <i class='{% trans %}crud.edit.icon{% endtrans %}'></i>
                        {% trans %}crud.edit.text{% endtrans %}
                    </a>
                    <?php if ($with_voter) {?>
                        {% endif  %}
                    <?php }?>
                    
                    
                </td>
            </tr>
        {% else %}
            <tr>
                <td colspan="<?= (count($entity_fields) + 1) ?>">{% trans %}crud.list.none{% endtrans %}</td>
            </tr>
        {% endfor %}
        </tbody>
    </table>

    <a class='btn btn-block btn-primary ' href="{{ path('<?= $route_name ?>_new') }}"> <i class='{% trans %}crud.new.icon{% endtrans %}'></i> {% trans %}crud.new.text{% endtrans %}</a>
    <a class='btn btn-block btn-primary ' href="{{ path('<?= $route_name ?>_profile') }}">{% trans %}user.profile{% endtrans %}</a>    
{% endblock %}
