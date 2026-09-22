<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
	<?php
	$this->load->view('admin/theme/meta.php');
	$this->load->view('admin/theme/header.php');
	?>
	<title>
		<?php if ($title) {
			echo 'POS ร้านเบ็ดเตล็ด | ' . $title;
		} else {
			echo 'POS ร้านเบ็ดเตล็ด';
		} ?>
	</title>

</head>

<body>
	<?php $this->load->view('admin/theme/menu.php'); ?>

	<div id="main">
		<?php $this->load->view('admin/theme/navbar.php'); ?>
		<?php
		if ($content) {
			$this->load->view('admin/' . $content);
		}
		?>
	</div>
</body>

<?php
// include
$this->load->view('admin/theme/footer.php');
$this->load->view('admin/theme/sweetalert.php')
?>

<?php
if (!empty($script)) {
	$this->load->view('admin/script/' . $script);
}
?>

</html>