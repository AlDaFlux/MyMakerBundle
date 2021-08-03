{% extends "base.html.twig" %}


{% block body %}
    <h1><a href="{{ path('<?= $route_name ?>_index') }}"><?= $entity_class_name ?></a></h1>

    <table class="table table-bordered">
        <tbody>
<?php foreach ($entity_fields as $field): ?>
            <tr>
                <th><?= ucfirst($field['fieldName']) ?></th>
                <td>{{ <?= $helper->getEntityFieldPrintCode($entity_twig_var_singular, $field) ?> }}</td>
            </tr>
<?php endforeach; ?>
        </tbody>
    </table>

     

    <div class='row'>
    <div class='col-md-6'>
                    <?php if ($with_voter) {?>
                        {% if is_granted('edit', <?= $entity_twig_var_singular ?>) %}
                    <?php }?>
        {{ include('<?= $templates_path ?>/_delete_form.html.twig') }}
                            <?php if ($with_voter) {?>
                        {% endif  %}
                    <?php }?>

    </div>
    <div class='col-md-6'>
    
                    <?php if ($with_voter) {?>
                        {% if is_granted('edit', <?= $entity_twig_var_singular ?>) %}
                    <?php }?>
        <a class='btn btn-block btn-warning' href="{{ path('<?= $route_name ?>_edit', {'<?= $entity_identifier ?>': <?= $entity_twig_var_singular ?>.<?= $entity_identifier ?>}) }}">
                                <i class='{% trans %}crud.edit.icon{% endtrans %}'></i>
                        {% trans %}crud.edit.text{% endtrans %}
                    <?php if ($with_voter) {?>
                        {% endif  %}
                    <?php }?>

        </a>
    </div>
    </div>
        

{% endblock %}
