<?= $helper->getHeadPrintCode('New '.$entity_class_name) ?>

{% block body %}
    <h1><a href="{{ path('<?= $route_name ?>_index') }}"><?= $human_word_entity_name_plural ?></a></h1>

    <h2>
        <i class='{% trans %}crud.new.icon{% endtrans %}'></i> {% trans %}crud.new.text{% endtrans %} <?= $human_word_entity_name_singular ?>
    </h2>
    
    
    {{ include('<?= $templates_path ?>/_form.html.twig') }}

{% endblock %}
