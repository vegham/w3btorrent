<div>
<img src="<?php echo $CONFIG['icon']['files']['delete']; ?>" alt="DELETE" class="disabled" title="Delete" id="delete" onclick="if ($(this).hasClass('hand')) { fileAction('delete'); }" />
<br /><br />
</div>
<div id="tree"></div>
<table><thead><tr><td>Name</td><td>Size</td><td>Date</td></tr></thead><tbody id="filesTableBody"></tbody></table>
<script type="text/javascript">
updateFilesPage('<?php echo json_encode($list); ?>');
$(document).ready(makeFilesTableClickable());
</script>