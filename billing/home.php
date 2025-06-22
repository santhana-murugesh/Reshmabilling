<?php include '../db_connect.php' ?>
<?php
function generateOrderNumber($conn) {
    $query = $conn->query("SELECT MAX(CAST(SUBSTRING(order_number, 4) AS UNSIGNED)) as max_num FROM orders");
    $result = $query->fetch_assoc();
    $next_num = $result['max_num'] + 1;
    return 'ORD' . str_pad($next_num, 6, '0', STR_PAD_LEFT);
}

if(isset($_GET['id'])) {
    $order = $conn->query("SELECT * FROM orders WHERE id = " . intval($_GET['id']));
    if($order->num_rows > 0) {
        foreach($order->fetch_array() as $k => $v) {
            $$k = $v;
        }
        $items = $conn->query("SELECT o.*, p.name FROM order_items o 
                             INNER JOIN products p ON p.id = o.product_id 
                             WHERE o.order_id = " . intval($id));
    } else {
        header("Location: ../index.php");
        exit();
    }
} else {
    $order_number = generateOrderNumber($conn);
}
?>
<style>
    :root {
        --primary-color: #4361ee;
        --secondary-color: #3f37c9;
        --accent-color: #4895ef;
        --success-color: #4cc9f0;
        --danger-color: #f72585;
        --light-color: #f8f9fa;
        --dark-color: #212529;
    }
    
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f5f7fa;
    }
    
    .card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: none;
        overflow: hidden;
    }
    
    .card-header {
        background-color: var(--primary-color);
        color: white;
        font-weight: 600;
        padding: 15px 20px;
        border-bottom: none;
    }
    
    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .btn-primary:hover {
        background-color: var(--secondary-color);
        border-color: var(--secondary-color);
    }
    
    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }
    
    .btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #212529;
    }
    
    .btn-danger {
        background-color: var(--danger-color);
        border-color: var(--danger-color);
    }
    
    .prod-item {
        cursor: pointer;
        transition: all 0.3s ease;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 15px;
        background-color: white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        height: 100%;
    }
    
    .prod-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    }
    
    .prod-item img {
        width: 100%;
        height: 120px;
        object-fit: cover;
    }
    
    .prod-item .card-body {
        padding: 10px;
    }
    
    .prod-item .card-text {
        font-weight: 500;
        margin-bottom: 5px;
    }
    
    .prod-item .price {
        color: var(--primary-color);
        font-weight: 600;
    }
    
    #o-list {
        background-color: white;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        max-height: 400px;
        overflow-y: auto;
    }
    
    .order-item {
        border-bottom: 1px solid #eee;
        padding: 10px 0;
    }
    
    .order-item:last-child {
        border-bottom: none;
    }
    
    .btn-qty {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        font-weight: bold;
    }
    
    .qty-input {
        width: 40px;
        text-align: center;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin: 0 5px;
    }
    
    .btn-remove {
        color: var(--danger-color);
        background: none;
        border: none;
        font-size: 1.2rem;
    }
    
    .btn-remove:hover {
        color: #d1144a;
    }
    
    .summary-card {
        background-color: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    
    .summary-total {
        font-weight: 600;
        font-size: 1.2rem;
        color: var(--primary-color);
        border-top: 1px solid #eee;
        padding-top: 10px;
        margin-top: 10px;
    }
    
    .category-btn {
        margin-right: 10px;
        margin-bottom: 10px;
        border-radius: 20px;
        padding: 5px 15px;
    }
    
    .category-btn.active {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    #prod-list {
        padding: 15px;
    }
    
    .discount-badge {
        background-color: #28a745;
        color: white;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 0.8rem;
        margin-left: 5px;
    }
    
    .modal-header {
        background-color: var(--primary-color);
        color: white;
    }
    
    @media (max-width: 768px) {
        .prod-item {
            margin-bottom: 10px;
        }
    }
</style>

<div class="container-fluid">
    <div class="row">
        <!-- Products Column -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Products</span>
                        <a class="btn btn-sm btn-light" href="../index.php">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex flex-wrap" id="cat-list">
                            <button class="btn btn-outline-primary category-btn active" data-id='all'>All</button>
                            <?php 
                            $qry = $conn->query("SELECT * FROM categories order by name asc");
                            while($row=$qry->fetch_assoc()):
                            ?>
                            <button class="btn btn-outline-primary category-btn" data-id='<?php echo $row['id'] ?>'>
                                <?php echo ucwords($row['name']) ?>
                            </button>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    
                    <div class="row" id="prod-list">
                        <?php
                        $prod = $conn->query("SELECT * FROM products where status = 1 order by name asc");
                        while($row=$prod->fetch_assoc()):
                        ?>
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                            <div class="card prod-item h-100" data-json='<?php echo json_encode($row) ?>' data-category-id="<?php echo $row['category_id'] ?>">
                                <img src="../uploads/products/<?php echo $row['image_path'] ?>" class="card-img-top" alt="<?php echo $row['name'] ?>">
                                <div class="card-body p-2 text-center">
                                    <h6 class="card-text mb-1"><?php echo $row['name'] ?></h6>
                                    <div class="price">₱<?php echo number_format($row['price'], 2) ?></div>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Order Summary Column -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Order Summary</span>
                        <button type="button" class="btn btn-sm btn-warning" id="apply-discount">
                            Apply Discount
                        </button>
                    </div>
                </div>
                
                <div class="card-body">
                    <form action="" id="manage-order">
                        <input type="hidden" name="id" value="<?php echo isset($_GET['id']) ? $_GET['id'] : '' ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Order Number</label>
                            <input type="text" class="form-control" name="order_number" value="<?php echo isset($order_number) ? $order_number : generateOrderNumber($conn) ?>" readonly required>
                        </div>
                        
                        <div id="o-list">
                            <div class="d-flex justify-content-between mb-2">
                                <h6 class="fw-bold">Items</h6>
                                <h6 class="fw-bold">Amount</h6>
                            </div>
                            
                            <div id="order-items-container">
                                <?php if(isset($items)): ?>
                                <?php while($row=$items->fetch_assoc()): ?>
                                <div class="order-item">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <div>
                                            <input type="hidden" name="item_id[]" value="<?php echo $row['id'] ?>">
                                            <input type="hidden" name="product_id[]" value="<?php echo $row['product_id'] ?>">
                                            <strong><?php echo ucwords($row['name']) ?></strong>
                                            <small class="text-muted">(₱<?php echo number_format($row['price'], 2) ?>)</small>
                                        </div>
                                        <span class="text-end">
                                            <input type="hidden" name="price[]" value="<?php echo $row['price'] ?>">
                                            <input type="hidden" name="amount[]" value="<?php echo $row['amount'] ?>">
                                            ₱<span class="amount"><?php echo number_format($row['amount'], 2) ?></span>
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <button type="button" class="btn btn-sm btn-outline-secondary btn-qty btn-minus">-</button>
                                            <input type="number" name="qty[]" class="form-control form-control-sm qty-input" value="<?php echo $row['qty'] ?>">
                                            <button type="button" class="btn btn-sm btn-outline-secondary btn-qty btn-plus">+</button>
                                        </div>
                                        <button type="button" class="btn-remove btn-rem">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                                <?php endif; ?>
                            </div>
                            
                            <?php if(!isset($items)): ?>
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                                <p>No items added yet</p>
                                <p>Click on products to add them to order</p>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="summary-card mt-3">
                            <div class="summary-row">
                                <span>Subtotal:</span>
                                <span>₱<span id="subtotal_amount">0.00</span></span>
                            </div>
                            <div class="summary-row">
                                <span>Discount <span id="discount-percent-display">(0%)</span>:</span>
                                <span>
                                    -₱<span id="discount_display">0.00</span>
                                    <input type="hidden" name="discount_percent" value="0">
                                    <input type="hidden" name="discount_amount" value="0">
                                </span>
                            </div>
                            <div class="summary-row summary-total">
                                <span>Total:</span>
                                <span>
                                    ₱<span id="total_amount">0.00</span>
                                    <input type="hidden" name="total_amount" value="0">
                                    <input type="hidden" name="total_tendered" value="0">
                                </span>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 mt-3">
                            <button type="button" class="btn btn-primary btn-lg" id="pay">
                                <i class="fas fa-print"></i> Print Receipt
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="pay_modal" tabindex="-1" role="dialog" aria-labelledby="payModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="payModalLabel">Payment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Amount Payable</label>
                    <input type="text" class="form-control text-end" id="apayable" readonly value="0.00">
                </div>
                <div class="mb-3">
                    <label class="form-label">Amount Received</label>
                    <input type="text" class="form-control text-end" id="tendered" value="" autocomplete="off">
                </div>
                <div class="mb-3">
                    <label class="form-label">Change</label>
                    <input type="text" class="form-control text-end" id="change" value="0.00" readonly>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" form="manage-order">Confirm & Print</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        // Initialize calculations
        calc();
        
        // Product click handler
        $('#prod-list .prod-item').click(function(){
            var data = $(this).attr('data-json');
            data = JSON.parse(data);
            
            // Check if product already exists in order
            var existingItem = $(`#order-items-container .order-item input[name="product_id[]"][value="${data.id}"]`).closest('.order-item');
            
            if(existingItem.length > 0){
                // Increment quantity if product exists
                var qtyInput = existingItem.find('[name="qty[]"]');
                qtyInput.val(parseInt(qtyInput.val()) + 1).trigger('change');
                calc();
                return false;
            }
            
            // Create new order item
            var itemHtml = `
                <div class="order-item">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div>
                            <input type="hidden" name="item_id[]" value="">
                            <input type="hidden" name="product_id[]" value="${data.id}">
                            <strong>${data.name}</strong>
                            <small class="text-muted">(₱${parseFloat(data.price).toLocaleString("en-US",{style:'decimal',minimumFractionDigits:2,maximumFractionDigits:2})})</small>
                        </div>
                        <span class="text-end">
                            <input type="hidden" name="price[]" value="${data.price}">
                            <input type="hidden" name="amount[]" value="${data.price}">
                            ₱<span class="amount">${parseFloat(data.price).toLocaleString("en-US",{style:'decimal',minimumFractionDigits:2,maximumFractionDigits:2})}</span>
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-qty btn-minus">-</button>
                            <input type="number" name="qty[]" class="form-control form-control-sm qty-input" value="1">
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-qty btn-plus">+</button>
                        </div>
                        <button type="button" class="btn-remove btn-rem">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            
            // Add to order items container
            $('#order-items-container').append(itemHtml);
            
            // If this is the first item, remove the empty state message
            if($('#order-items-container .order-item').length === 1) {
                $('#o-list .text-center').remove();
            }
            
            // Initialize quantity handlers
            qty_func();
            calc();
        });
        
        // Quantity handlers
        function qty_func(){
            $('#order-items-container').on('click', '.btn-minus', function(){
                var qtyInput = $(this).siblings('[name="qty[]"]');
                var qty = parseInt(qtyInput.val());
                qty = qty > 1 ? qty - 1 : 1;
                qtyInput.val(qty).trigger('change');
                calc();
            });
            
            $('#order-items-container').on('click', '.btn-plus', function(){
                var qtyInput = $(this).siblings('[name="qty[]"]');
                var qty = parseInt(qtyInput.val()) + 1;
                qtyInput.val(qty).trigger('change');
                calc();
            });
            
            $('#order-items-container').on('click', '.btn-rem', function(){
                $(this).closest('.order-item').remove();
                calc();
                
                // Show empty state if no items left
                if($('#order-items-container .order-item').length === 0) {
                    $('#order-items-container').html(`
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                            <p>No items added yet</p>
                            <p>Click on products to add them to order</p>
                        </div>
                    `);
                }
            });
            
            $('#order-items-container').on('change input', '[name="qty[]"]', function(){
                var qty = $(this).val();
                if(qty < 1) {
                    qty = 1;
                    $(this).val(1);
                }
                
                var item = $(this).closest('.order-item');
                var price = item.find('[name="price[]"]').val();
                var amount = parseFloat(qty) * parseFloat(price);
                
                item.find('[name="amount[]"]').val(amount);
                item.find('.amount').text(parseFloat(amount).toLocaleString("en-US",{style:'decimal',minimumFractionDigits:2,maximumFractionDigits:2}));
                calc();
            });
        }
        
        // Calculate totals
        function calc(){
            var subtotal = 0;
            
            $('[name="amount[]"]').each(function(){
                subtotal += parseFloat($(this).val());
            });
            
            // Display Subtotal
            $('#subtotal_amount').text(subtotal.toLocaleString("en-US", { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            
            // Calculate Discount
            var discountPercent = parseFloat($('[name="discount_percent"]').val());
            var discountAmount = subtotal * (discountPercent / 100);
            
            $('[name="discount_amount"]').val(discountAmount);
            $('#discount_display').text(discountAmount.toLocaleString("en-US", { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $('#discount-percent-display').text(`(${discountPercent}%)`);
            
            // Calculate Final Total
            var total = subtotal - discountAmount;
            $('[name="total_amount"]').val(total);
            $('#total_amount').text(total.toLocaleString("en-US", { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $('#apayable').val(total.toLocaleString("en-US", { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        }
        
        // Discount button
        $('#apply-discount').click(function() {
            var discountPercent = parseFloat($('[name="discount_percent"]').val());
            
            // Toggle between 10% and 0% discount
            if (discountPercent === 10) {
                $('[name="discount_percent"]').val(0);
                $(this).removeClass('btn-success').addClass('btn-warning').text('Apply Discount');
            } else {
                $('[name="discount_percent"]').val(10);
                $(this).removeClass('btn-warning').addClass('btn-success').text('Remove Discount');
            }
            
            // Recalculate total
            calc();
        });
        
        // Category filter
        $('.category-btn').click(function(){
            $('.category-btn').removeClass('active');
            $(this).addClass('active');
            
            var id = $(this).attr('data-id');
            
            if(id == 'all'){
                $('.prod-item').parent().show();
            } else {
                $('.prod-item').each(function(){
                    if($(this).attr('data-category-id') == id) {
                        $(this).parent().show();
                    } else {
                        $(this).parent().hide();
                    }
                });
            }
        });
        
        // Print button
        $('#pay').click(function(){
            if($('#order-items-container .order-item').length <= 0){
                alert_toast("Please add at least 1 product first.",'danger');
                return false;
            }
            
            $('#pay_modal').modal('show');
            setTimeout(function(){
                $('#tendered').val('').trigger('change');
                $('#tendered').focus();
            }, 500);
        });
        
        // Tendered amount handling
        $('#tendered').on('input', function(e){
            var tend = $(this).val().replace(/,/g, '');
            $('[name="total_tendered"]').val(tend);
            
            if(tend == '') {
                $(this).val('');
            } else {
                $(this).val(parseFloat(tend).toLocaleString("en-US"));
            }
            
            tend = tend > 0 ? tend : 0;
            var amount = $('[name="total_amount"]').val();
            var change = parseFloat(tend) - parseFloat(amount);
            
            $('#change').val(parseFloat(change).toLocaleString("en-US", { 
                style: 'decimal', 
                minimumFractionDigits: 2, 
                maximumFractionDigits: 2 
            }));
        });
        
        // Form submission
        $('#manage-order').submit(function(e){
            e.preventDefault();
            
            if($('#order-items-container .order-item').length <= 0){
                alert_toast("Please add at least 1 product first.",'danger');
                return false;
            }
            
            start_load();
            
            $.ajax({
                url: '../ajax.php?action=save_order',
                method: 'POST',
                data: $(this).serialize(),
                success: function(resp){
                    if(resp > 0){
                        if($('[name="total_tendered"]').val() > 0){
                            alert_toast("Order saved successfully.",'success');
                            
                            setTimeout(function(){
                                var nw = window.open('../receipt.php?id='+resp, "_blank", "width=900,height=600");
                                
                                setTimeout(function(){
                                    nw.print();
                                    setTimeout(function(){
                                        nw.close();
                                        location.reload();
                                    }, 500);
                                }, 500);
                            }, 500);
                        } else {
                            alert_toast("Order saved successfully.",'success');
                            setTimeout(function(){
                                location.reload();
                            }, 500);
                        }
                    }
                }
            });
        });
    });
</script>