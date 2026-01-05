<?php if ($extend) {?>{% extends "<?= $extend ?>" %}
<?php }?>
    
{% block body %}
    <h1><a href="{{ path('<?= $route_name ?>_index') }}"><?= $human_word_entity_name ?></a></h1>

    <table class="table table-bordered table-triable">
        <thead>
            <tr>
<?php foreach ($entity_fields as $field): ?>
                <th><?= ucfirst($field['displayFieldName']) ?></th>
<?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
        <?php $first = true ?>
        {% for <?= $entity_twig_var_singular ?> in <?= $entity_twig_var_plural ?> %}
            <tr>
<?php foreach ($entity_fields as $field): ?><?php if ($field["type"]=="boolean") { ?>

                    <td class='center'>
                        {% if <?= $entity_twig_var_singular ?>.<?=  $field["fieldName"] ?> %}
                            <i class='{{'crud.boolean.true_classe'|trans}}'>
                        {% else %}
                            <i class='{{'crud.boolean.false_classe'|trans}}'>
                        {% endif  %}
                    </td><?php } else { ?>
                    
                    <td><?php if ($first) { ?><a href="{{ path('<?= $route_name ?>_show', {'<?= $entity_identifier ?>': <?= $entity_twig_var_singular ?>.<?= $entity_identifier ?>}) }}"><?php } ?>{{ <?= $helper->getEntityFieldPrintCode($entity_twig_var_singular, $field) ?> }}<?php if ($first) { ?></a><?php } ?></td><?php } ?><?php $first = false ; endforeach; ?>

            </tr>
        {% else %}
            <tr>
                <td colspan="<?= (count($entity_fields) + 1) ?>">{% trans %}crud.list.none{% endtrans %}</td>
            </tr>
        {% endfor %}
        </tbody>
    </table>

    <?php if ($with_voter) {?>
    {% if is_granted('add_<?= $entity_twig_var_singular ?>') %}
        <?php }?>
    <a class='btn btn-block btn-primary ' href="{{ path('<?= $route_name ?>_new') }}"> <i class='{% trans %}crud.new.icon{% endtrans %}'></i> {% trans %}crud.new.text{% endtrans %}</a>
    <?php if ($with_voter) {?>
    {% endif %}
        <?php }?>
    
{% endblock %}
