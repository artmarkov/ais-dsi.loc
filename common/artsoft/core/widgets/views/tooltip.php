<!--<span data-toggle="tooltip" data-placement="bottom" title=""
      data-original-title="<?/*= $v['message'] */?>">
 <i class="fa fa-<?/*= $v['icon'] */?>" style="color: <?/*= $v['color'] */?>; aria-hidden="true"></i>
</span>-->

<!--<a title="<?/*= $v['message'] */?>">
 <i class="fa fa-<?/*= $v['icon'] */?>" style="color: <?/*= $v['color'] */?>; aria-hidden="true"></i>
</a>-->
<?php
$helpIcon = \artsoft\helpers\Html::beginTag('span', [
'title' => $v['message'],
'data-content' => $v['message'],
'data-html' => 'true',
'role' => 'button',
'style' => 'margin-bottom: 5px; padding: 0 5px;',
'class' => 'btn btn-sm role-help-btn',
]);
$helpIcon .= '<i class="fa fa-' . $v['icon'] . '" style="color: ' . $v['color'] . ';" aria-hidden="true"></i>';
$helpIcon .= \artsoft\helpers\Html::endTag('span');

echo $helpIcon;