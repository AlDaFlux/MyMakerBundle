<?= $helper->getHeadPrintCode($entity_class_name) ?>

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
    

{% endblock %}
