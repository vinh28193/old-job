<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var $attribute array */
/** @var $model \yii\db\ActiveRecord */

?>
<div class="table-wrap">

	<?php if($attribute['page'] == 'jobtype') { ?>

		<div class="btn-box">
			<a class="btn btn-danger pjaxModal" href="pjax-modal?flg=first" title="<?= Yii::t('app', '追加') ?>"><i class="glyphicon glyphicon-plus"></i><?= Yii::t('app', '大カテゴリ') ?></a>
			<a class="btn btn-danger pjaxModal" href="pjax-modal?flg=second" title="<?= Yii::t('app', '追加') ?>"><i class="glyphicon glyphicon-plus"></i><?= Yii::t('app', 'カテゴリ') ?></a>
			<a class="btn btn-danger pjaxModal" href="pjax-modal?flg=thread" title="<?= Yii::t('app', '追加') ?>"><i class="glyphicon glyphicon-plus"></i><?= Yii::t('app', '項目') ?></a>
		</div>

		<table class="table table-bordered result-style type02">
			<tbody>

			<?php foreach($model as $first): ?>
				<tr>
					<td class="<?php if($first->valid_chk == 0){ echo 'disabled '; } ?>category-cel" colspan="2">
						<a class="pjaxModal" href="pjax-modal?id=<?= Html::encode($first->id); ?>&flg=first" title="変更">
							<?php echo $first->valid_chk == "1" ? "<span class=\"label label-success\" style=\"margin-right:5px;\">".Yii::t('app', '公開中')."</span>" : "<span class=\"label label-default\" style=\"margin-right:5px;\">".Yii::t('app', '非公開')."</span>"; ?>
							<?php echo Html::encode($first->name); ?>
						</a>
					</td>
				</tr>

				<tr>
					<th><?= Yii::t('app', 'カテゴリ') ?></th>
					<th><?= Yii::t('app', '項目') ?></th>
				</tr>

				<?php
				$seconds = $first->{$attribute['relation'][0]};
				foreach($seconds as $second):
					?>
					<tr>
						<td <?php if($first->valid_chk == 0 || $second->valid_chk == 0){ echo 'class="disabled"'; } ?> rowspan="<?php echo count($second->{$attribute['relation'][1]}) + 1; ?>">
							<a class="pjaxModal" href="pjax-modal?id=<?= Html::encode($second->id); ?>&flg=second" title="変更">
								<?php echo $second->valid_chk == "1" ? "<span class=\"label label-success\" style=\"margin-right:5px;\">".Yii::t('app', '公開中')."</span>" : "<span class=\"label label-default\" style=\"margin-right:5px;\">".Yii::t('app', '非公開')."</span>"; ?>
								<?php echo Html::encode($second->job_type_big_name); ?>
							</a>
						</td>
					</tr>

					<?php
					$thirds = $second->{$attribute['relation'][1]};
					foreach($thirds as $thread):
						?>
						<tr>
							<td <?php if($first->valid_chk == 0 || $second->valid_chk == 0 || $thread->valid_chk == 0){ echo 'class="disabled"'; } ?>>
								<a class="pjaxModal" href="pjax-modal?id=<?= Html::encode($thread->id); ?>&flg=thread" title="変更">
									<?php echo $thread->valid_chk == "1" ? "<span class=\"label label-success\" style=\"margin-right:5px;\">".Yii::t('app', '公開中')."</span>" : "<span class=\"label label-default\" style=\"margin-right:5px;\">".Yii::t('app', '非公開')."</span>"; ?>
									<?php echo Html::encode($thread->job_type_small_name); ?>
								</a>
							</td>
						</tr>
					<?php endforeach; ?>

				<?php endforeach; ?>

			<?php endforeach; ?>

			</tbody>
		</table>

	<?php }else if($attribute['page'] == 'wage') { ?>

		<table class="table table-bordered result-style type02">
			<thead>
			<tr>
				<th><?= Yii::t('app', 'カテゴリ') ?>
					<a class="pjaxModal" href="pjax-modal?flg=first" title="<?= Yii::t('app', '追加') ?>"><i class="glyphicon glyphicon-plus"></i></a>
				</th>
				<th><?= Yii::t('app', '項目') ?>
					<a class="pjaxModal" href="pjax-modal?flg=second" title="<?= Yii::t('app', '追加') ?>"><i class="glyphicon glyphicon-plus"></i></a>
				</th>
			</tr>
			</thead>

			<tbody>
			<?php foreach($model as $first): ?>
				<tr>
					<td <?php if($first->valid_chk == 0){ echo 'class="disabled"'; } ?> rowspan="<?php echo count($first->{$attribute['relation']}) + 1; ?>">
						<a class="pjaxModal" href="pjax-modal?id=<?= Html::encode($first->id); ?>&flg=first" title="<?= Yii::t('app', '変更') ?>">
							<?php echo $first->valid_chk == "1" ? "<span class=\"label label-success\" style=\"margin-right:5px;\">".Yii::t('app', '公開中')."</span>" : "<span class=\"label label-default\" style=\"margin-right:5px;\">".Yii::t('app', '非公開')."</span>"; ?>
							<?php echo Html::encode($first->wage_category_name); ?>
						</a>
					</td>
				</tr>

				<?php foreach($first->{$attribute['relation']} as $second): ?>
					<tr>
						<td <?php if($first->valid_chk == 0 || $second->valid_chk == 0){ echo 'class="disabled"'; } ?>>
							<a class="pjaxModal" href="pjax-modal?id=<?= Html::encode($second->id); ?>&flg=second" title="<?= Yii::t('app', '変更') ?>">
								<?php echo $second->valid_chk == "1" ? "<span class=\"label label-success\" style=\"margin-right:5px;\">".Yii::t('app', '公開中')."</span>" : "<span class=\"label label-default\" style=\"margin-right:5px;\">".Yii::t('app', '非公開')."</span>"; ?>
								<?php echo Html::encode($second->disp_price); ?>
							</a>
						</td>
					</tr>

				<?php endforeach; ?>

			<?php endforeach; ?>
			</tbody>
		</table>

	<?php }else if($attribute['page'] == 'searchkey1') { ?>

		<table class="table table-bordered result-style type02">
			<thead>
			<tr>
				<th><?= Yii::t('app', '項目') ?>
					<a class="pjaxModal" href="pjax-modal?flg=first" title="<?= Yii::t('app', '追加') ?>"><i class="glyphicon glyphicon-plus"></i></a>
				</th>
			</tr>
			</thead>

			<tbody>
			<?php foreach($model as $first): ?>
				<tr>
					<td <?php if($first->valid_chk == 0){ echo 'class="disabled"'; } ?>>
						<a class="pjaxModal" href="pjax-modal?id=<?= Html::encode($first->id); ?>&flg=first" title="<?= Yii::t('app', '変更') ?>">
							<?php echo $first->valid_chk == "1" ? "<span class=\"label label-success\" style=\"margin-right:5px;\">".Yii::t('app', '公開中')."</span>" : "<span class=\"label label-default\" style=\"margin-right:5px;\">".Yii::t('app', '非公開')."</span>"; ?>
							<?php echo Html::encode($first->searchkey_item_name); ?>
						</a>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>

	<?php }else if($attribute['page'] == 'searchkey2') { ?>

		<table class="table table-bordered result-style type02">
			<thead>
			<tr>
				<th><?= Yii::t('app', 'カテゴリ') ?>
					<a class="pjaxModal" href="pjax-modal?flg=first" title="<?= Yii::t('app', '追加') ?>"><i class="glyphicon glyphicon-plus"></i></a>
				</th>
				<th><?= Yii::t('app', '項目') ?>
					<a class="pjaxModal" href="pjax-modal?flg=second" title="<?= Yii::t('app', '追加') ?>"><i class="glyphicon glyphicon-plus"></i></a>
				</th>
			</tr>
			</thead>

			<tbody>
			<?php foreach($model as $first): ?>
				<tr>
					<td <?php if($first->valid_chk == 0){ echo 'class="disabled"'; } ?> rowspan="<?php echo count($first->{$attribute['relation']}) + 1; ?>">
						<a class="pjaxModal" href="pjax-modal?id=<?= Html::encode($first->id); ?>&flg=first" title="<?= Yii::t('app', '変更') ?>">
							<?php echo $first->valid_chk == "1" ? "<span class=\"label label-success\" style=\"margin-right:5px;\">".Yii::t('app', '公開中')."</span>" : "<span class=\"label label-default\" style=\"margin-right:5px;\">".Yii::t('app', '非公開')."</span>"; ?>
							<?php echo Html::encode($first->searchkey_category_name); ?>
						</a>
					</td>
				</tr>

				<?php foreach($first->{$attribute['relation']} as $second): ?>
					<tr>
						<td <?php if($first->valid_chk == 0 || $second->valid_chk == 0){ echo 'class="disabled"'; } ?>>
							<a class="pjaxModal" href="pjax-modal?id=<?= Html::encode($second->id); ?>&flg=second" title="<?= Yii::t('app', '変更') ?>">
								<?php echo $second->valid_chk == "1" ? "<span class=\"label label-success\" style=\"margin-right:5px;\">".Yii::t('app', '公開中')."</span>" : "<span class=\"label label-default\" style=\"margin-right:5px;\">".Yii::t('app', '非公開')."</span>"; ?>
								<?php echo Html::encode($second->searchkey_item_name); ?>
							</a>
						</td>
					</tr>

				<?php endforeach; ?>

			<?php endforeach; ?>
			</tbody>
		</table>

	<?php } ?>

</div>