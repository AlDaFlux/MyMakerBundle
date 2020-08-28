<?= $helper->getHeadPrintCode('Edit '.$entity_class_name) ?>

{% block body %}
    <h1><a href="{{ path('<?= $route_name ?>_index') }}"><?= $entity_class_name ?></a></h1>
    <h2>
        <i class='{% trans %}crud.edit.icon{% endtrans %}'></i> {% trans %}crud.edit.text{% endtrans %} <?= $entity_class_name ?>
    </h2>
    {{ include('<?= $templates_path ?>/_form.html.twig', {'button_label': 'Update'}) }}


{% endblock %}
