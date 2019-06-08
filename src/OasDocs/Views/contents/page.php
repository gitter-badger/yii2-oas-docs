<div class="container-fluid">
    <div id="OasDocs" class="row">
        <h2><?= $this->params['title'] ?></h2>
        <div id="docsLeft" class="col-md-5">
            <?php echo $this->render('_toc', ['toc' => $toc]) ?>
        </div>
        <div id="docsRight" class="col-md-7">
            <?php echo $this->render(
                $partial,
                [
                    'data' => $data,
                    'id' => $id,
                    'section' => $toc->currentSection()
                ]
            ) ?>
        </div>
    </div>
</div>
