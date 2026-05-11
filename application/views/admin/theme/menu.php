<!-- Sidebar styles managed by pos-modern.css -->


<?php
// กรณีมีเมนูย่อย 
// $category = $this->db->order_by('category_sort', 'ASC')->get('category')->result_array();

// $categorySubmenu = array_map(function ($cat) {
// 	return [
// 		'slug' => 'category' . $cat['category_id'],
// 		'label' =>  $cat['category_name'],
// 		'url' => admin_url('sub-category/' . $cat['category_id']),
// 		'icon' => $cat['category_icon'] ?: null  // fallback เผื่อไม่มี
// 	];
// }, $category);
// เมนูระบบ POS ร้านขายของเบ็ดเตล็ด
$permission = isset($this->session->userdata('_auth')['admin_permission']) ? $this->session->userdata('_auth')['admin_permission'] : 0;

if ($permission == 0) {
	// พนักงานขาย - เห็นเฉพาะ POS และประวัติการขาย
	$menuArr = [
		[
			'slug' => 'dashboard',
			'label' => 'หน้าหลัก',
			'icon' => 'fas fa-tachometer-alt',
			'url' => admin_url('')
		],
		[
			'slug' => 'pos',
			'label' => 'ขายสินค้า (POS)',
			'icon' => 'fas fa-cash-register',
			'url' => admin_url('pos')
		],
		[
			'slug' => 'sales',
			'label' => 'ประวัติการขาย',
			'icon' => 'fas fa-receipt',
			'url' => admin_url('sales')
		],
	];
} else {
	// ผู้จัดการ/เจ้าของร้าน - เห็นทุกเมนู
	$menuArr = [
		[
			'slug' => 'dashboard',
			'label' => 'หน้าหลัก',
			'icon' => 'fas fa-tachometer-alt',
			'url' => admin_url('')
		],
		[
			'slug' => 'pos',
			'label' => 'ขายสินค้า (POS)',
			'icon' => 'fas fa-cash-register',
			'url' => admin_url('pos')
		],
		[
			'slug' => 'sales',
			'label' => 'ประวัติการขาย',
			'icon' => 'fas fa-receipt',
			'url' => admin_url('sales')
		],
		[
			'slug' => 'product',
			'label' => 'จัดการสินค้า',
			'icon' => 'fas fa-boxes',
			'url' => admin_url('product')
		],
		[
			'slug' => 'category',
			'label' => 'หมวดหมู่สินค้า',
			'icon' => 'fas fa-th-list',
			'url' => admin_url('category')
		],
		[
			'slug' => 'stockmanage',
			'label' => 'จัดการสต๊อก',
			'icon' => 'fas fa-warehouse',
			'url' => admin_url('stockManage')
		],
		[
			'slug' => 'report',
			'label' => 'รายงาน/สรุปยอด',
			'icon' => 'fas fa-chart-bar',
			'url' => admin_url('report')
		],
		[
			'slug' => 'information',
			'label' => 'ข้อมูลร้านค้า',
			'icon' => 'fas fa-store',
			'url' => admin_url('information')
		],
		[
			'slug' => 'admin',
			'label' => 'จัดการพนักงาน',
			'icon' => 'fas fa-user-shield',
			'url' => admin_url('admin')
		],
	];
}
?>

<div id="sidebar" class="active">
	<div class="toggler">
		<a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
	</div>
	<div class="sidebar-wrapper active">
		<div class="sidebar-header">
			<div class="d-flex justify-content-center">
				<div class="logo text-center py-2">
					<a href="<?= admin_url(); ?>" class="text-decoration-none">
						<i class="fas fa-store fa-2x text-primary"></i>
						<h5 class="mt-2 mb-0 text-dark">POS ร้านเบ็ดเตล็ด</h5>
					</a>
				</div>
			</div>
		</div>
		<div class="sidebar-menu">
			<ul class="menu">
				<hr>
				<?php foreach ($menuArr as $menu): ?>
					<li class="sidebar-item 
			<?= ($menu_slug == $menu['slug'] || (!empty($menu['submenu']) && in_array($menu_slug, array_column($menu['submenu'], 'slug')))) ? 'active' : '' ?> 
			<?= !empty($menu['submenu']) ? 'has-sub' : '' ?>">
						<a href="<?= isset($menu['url']) ? $menu['url'] : '#' ?>" class='sidebar-link'>
							<i class="<?= $menu['icon'] ?>"></i>
							<span><?= $menu['label'] ?></span>
						</a>

						<?php if (!empty($menu['submenu'])): ?>
							<ul
								class="submenu <?= in_array($menu_slug, array_column($menu['submenu'], 'slug')) ? 'active' : '' ?>">
								<?php foreach ($menu['submenu'] as $submenu): ?>
									<li class="submenu-item <?= ($menu_slug == $submenu['slug']) ? 'active' : '' ?>">
										<a href="<?= $submenu['url'] ?>">
											<span>- <?php if (!empty($submenu['icon'])): ?><i
														class="<?= $submenu['icon'] ?>"></i><?php endif; ?>
												<?= $submenu['label'] ?></span>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
	</div>
</div>