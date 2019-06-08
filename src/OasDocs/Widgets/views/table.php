<table class="table table-striped">
    <thead>
    <?php foreach ($widget->columns as $colAttrs) : ?>
        <th><?= $colAttrs['display'] ?></th>
    <?php endforeach ?>
    </thead>
    <tbody>
    <?php foreach ($widget->rows as $row) : ?>
        <tr>
            <?php foreach ($widget->columns as $fieldName => $colAttrs) : ?>
                <td><?= $widget->field($fieldName, $row) ?></td>
            <?php endforeach ?>
        </tr>
    <?php endforeach ?>
    </tbody>
</table>
