<?php
require_once ('models/dphieunvl.php');

$records_per_page = 10;

// Tính tổng số trang
$total_pages = ceil(count($dphieunvl) / $records_per_page);

// Lấy trang hiện tại từ tham số truyền vào URL, mặc định là trang 1
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;

// Xác định vị trí bắt đầu của mỗi trang trong mảng đơn hàng
$start_index = ($current_page - 1) * $records_per_page;

// Lấy danh sách đơn hàng cho trang hiện tại
$dphieunvl_page = array_slice($dphieunvl, $start_index, $records_per_page);

if (isset($_POST['filterStatus'])) {
    $filterStatus = $_POST['filterStatus'];
    if ($filterStatus !== '') {
        $dphieunvl_page = dphieunvl::filterByStatus($filterStatus, $start_index, $records_per_page);
    }
}
?>

<script>
    function confirmDelete() {
        return confirm('Bạn có chắc chắn muốn xóa mục này không?');
    }
</script>



<!-- Thêm các ô vuông thống kê -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-info text-white h-100">
            <div class="card-body">
                
            
    <div class="text-center">
                    <h5 class="font-weight-bold">Tổng số phiếu</h5>
                    <p><?php echo $tongDonHang; ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-warning text-white h-100">
            <div class="card-body">
                <div class="text-center">
                    <h5 class="font-weight-bold">Số phiếu đã duyệt</h5>
                    <p><?php echo $soDonDaThanhToan ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-danger text-white h-100">
            <div class="card-body">
                <div class="text-center">
                    <h5 class="font-weight-bold">Số phiếu chưa duyệt</h5>
                    <p><?php echo $soDonChuaThanhToan ?></p>
                </div>
            </div>
        </div>
    </div>
</div>



<h1 class="h3 mb-2 text-center text-gray-800 ">Danh sách phiếu YC</h1>

<div class="card shadow mb-4">
<form method="post" action="" class="form-inline" style="margin-left:10px;margin-top:10px;">
    <div class="form-group mr-2">
        <label for="filterStatus" class="mr-2">Lọc theo trạng thái:</label>
        <select class="form-control" id="filterStatus" name="filterStatus">
            <option value="">Tất cả</option>
            <option value="1">Đã duyệt</option>
            <option value="0">Chưa duyệt</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Lọc</button>
</form>

    <div class="card-body">

    


        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                <tr>
                    <th>ID</th> <!-- Thay đổi cột ID thành cột STT -->
                    <th>Thời gian</th>
                    <th>Nhà Cung Cấp</th>
                    <th>Trạng Thái</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>ID</th> <!-- Thay đổi cột ID thành cột STT -->
                    <th>Thời gian</th>
                    <th>Nhà Cung Cấp</th>
                    <th>Trạng Thái</th>
                    <th>Action</th>
                </tr>
                </tfoot>
                <tbody>

                <?php
                 $stt = $start_index + 1; // Bắt đầu từ số 1
                foreach ($dphieunvl_page as $item) {
                    ?>
                    <form method="post">
                        <tr>
                            <td><?= $item->Id ?></td> <!-- Tăng giá trị của STT và hiển thị -->
                            <td><?= date('d/m/Y', strtotime($item->NgayLap)) ?></td>
                            <td><?= $item->IdNCC ?></td>
                            <td><?php
                                if ($item->TrangThai == "1") {
                                    echo "Đã duyệt";
                                } else {
                                    echo "Chưa duyệt";
                                }
                                ?></td>
                            <td>
                                <a href="index.php?controller=dphieunvl&action=show&id=<?= $item->Id ?>"
                                   class='btn btn-primary mr-3'>Chi tiết</a>

                                <a href="index.php?controller=dphieunvl&action=print&id=<?= $item->Id ?>"
                                   class='btn btn-primary mr-3'>In</a>

                                <button type="submit" name="dele" value="<?= $item->Id ?>" onclick="return confirmDelete()"
                                        class='btn btn-danger'>Xoá
                                </button>
                    </form>
                    </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>

            <!-- Hiển thị phân trang -->
<div class="row mt-4">
    <div class="col-md-12">
    <nav aria-label="Page navigation">
    <ul class="pagination justify-content-end">
        <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
            <li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                <a class="page-link" href="index.php?controller=dphieunvl&action=index&page=<?= $i ?>">
                    <?= $i ?>
                </a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
</div>
        </div>
    </div>
</div>
<?php
if (isset($_POST['dele'])) {
    $id = $_POST['dele'];

    // Gọi phương thức xóa và kiểm tra xác nhận
    $confirmation = dphieunvl::delete($id);

    if ($confirmation) {
        // Người dùng đã xác nhận, chuyển hướng tới trang danh sách
        header('location:index.php?controller=dphieunvl&action=index');
    } else {
        // Người dùng chưa xác nhận, hiển thị thông báo hoặc thực hiện các xử lý khác
        echo "<script>alert('Bạn đã hủy xóa.')</script>";
    }
}
?>
