<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use proseeds\assets\AdminAsset;
use yii\bootstrap\Modal;
use yii\bootstrap\Collapse;
use proseeds\widgets\TableForm;

AdminAsset::register($this);
?>
<?php $this->beginPage() ?>
	<!DOCTYPE html>
	<html lang="<?= Yii::$app->language ?>">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta charset="<?= Yii::$app->charset ?>">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<?= Html::csrfMetaTags() ?>
		<?php $this->head() ?>
	</head>

	<body class="drawer-responsive">
	<?php $this->beginBody() ?>
	<div id="wrapper">
		<!-- sidebar =============================================== -->
		<div class="drawer-overlay">
			<!-- navi ================================================== -->
			<!-- main ================================================== -->
			<div id="main">
				<div id="scroller">
					<div id="main_in">
						<h1 class="heading"><span
									class="glyphicon glyphicon-list-alt"></span><?= Html::encode($model->mail_name) ?></h1>
						<?php
						$tableForm = TableForm::begin([
								'action' => Url::to(['/manage/secure/settings/sendmail/update', 'id' => $model->id]),
								'options' => [
										'enctype' => 'multipart/form-data'
								],
								'tableOptions' => [
										'class' => 'table table-bordered'
								]
						]);
						$fieldOptions = ['tableHeaderOptions' => ['class' => 'w30']];

						?>

						<?php
						$tableForm->beginTable();
						echo $tableForm->row($model, 'mail_name', $fieldOptions)->text();

						echo $tableForm->row($model, 'mail_to', $fieldOptions)->text();

						echo $tableForm->row($model, 'from_name', $fieldOptions)->textInput(['maxlength' => 100]);

						echo $tableForm->row($model, 'from_address', $fieldOptions)->textInput(['maxlength' => true]);

						echo $tableForm->row($model, 'subject', $fieldOptions)->textInput(['maxlength' => 100]);

						echo $tableForm->row($model, 'contents', $fieldOptions)->textArea(['maxlength' => true, 'rows' => 8]);

						echo $tableForm->row($model, 'mail_sign', $fieldOptions)->textArea(['maxlength' => true, 'rows' => 8]);

						$tableForm->endTable();
						?>
						<div class="row mgt20">
							<p class="text-center">
								<?= Html::submitButton(Yii::t('app', '変更'), ['class' => 'btn btn-primary']) ?>
							</p>
						</div>
						<div class="replace_info">
							<?= Html::a(
									'<span class="glyphicon glyphicon-list-alt"></span>' . Yii::t('app', '使用できる置換文字列'),
									'#collapse_replace_info-' . $model->id,
									[
											'role' => 'button',
											'data-toggle' => "collapse",
											'aria-expanded' => "false",
											'aria-controls' => "collapse",
											'class' => 'pull-right',
									]); ?>
							<?= '<br>'; ?>
							<?= Html::tag(
									'div',
									$this->render('_collapse_replace_info', ['model' => $model]),
									[
											'id' => 'collapse_replace_info-' . $model->id,
											'class' => "collapse clearfix"
									]); ?>
						</div>


						<?php TableForm::end(); ?>
					</div>


				</div>
			</div>
			<!-- /main ================================================= -->
		</div>
	</div>
	<?php $this->endBody() ?>
	</body>
	</html>
<?php $this->endPage() ?>