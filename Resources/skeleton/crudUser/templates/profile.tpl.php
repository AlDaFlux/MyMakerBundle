<?php if ($extend) {?>
    {% extends "<?= $extend ?>" %}
<?php }?>
    


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

     

    <a class='btn btn-block btn-warning' href="{{ path('<?= $route_name ?>_change_my_password') }}"><i class='{% trans %}crud.change_password.icon{% endtrans %}'></i>{% trans %}crud.change_password.text{% endtrans %}</a>
        

{% endblock %}
