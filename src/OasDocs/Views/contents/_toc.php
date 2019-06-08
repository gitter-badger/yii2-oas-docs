<nav role="navigation" class="table-of-contents">
    <ol>
        <?php foreach ($toc->toArray() as $section) : ?>
            <?php echo $this->render('_toc_section', ['section' => $section]) ?>
        <?php endforeach ?>
    </ol>
</nav>
