<li class="<?= $section['currentPage'] ? 'active' : '' ?>">
    <?php if ($section['url'] != '#') : ?>
    <a href="<?= $section['url'] ?>"><?= $section['text'] ?></a>
    <?php else : ?>
    <?= $section['text'] ?>
    <?php endif ?>
    <?php if (isset($section['items']) && $section['items']) : ?>
    <ol>
        <?php foreach ($section['items'] as $item) : ?>
            <?php echo $this->render('_toc_section', ['section' => $item]) ?>
        <?php endforeach ?>
    </ol>
    <?php endif ?>
</li>
