<?php

// CSS
function myplugin_add_head_styles()
{
    echo '
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
    .bg-dark-blue {
        background-color: rgb(15, 58, 95);
    }
    .bg-pink {
        background-color: rgb(228, 168, 196);
    }
    .bg-azure {
        background-color: rgb(140, 224, 229);
    }
    thead, .text-danger {
        color: rgb(207, 46, 46)
    }
    #loading-spinner {
        display: none;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999;
      }
      
      #loading-spinner::after {
        content: "";
        display: block;
        position: absolute;
        top: 50%;
        left: 50%;
        width: 50px;
        height: 50px;
        margin: -25px 0 0 -25px;
        border: 5px solid #ccc;
        border-top-color: #333;
        border-radius: 50%;
        animation: spin 1s infinite linear;
      }
      
      @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
      }
    </style>';
}
add_action('wp_head', 'myplugin_add_head_styles');

// Script
function my_plugin_scripts()
{
    wp_enqueue_script('jquery');
    wp_enqueue_script( 'bootstrap-bundle', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', array( 'jquery' ), '5.3.2', true );
}
add_action('wp_enqueue_scripts', 'my_plugin_scripts');
  
// AJAX action to fetch data based on page length
add_action('wp_ajax_get_next_return_ajax', 'get_next_return_ajax_callback');
add_action('wp_ajax_nopriv_get_next_return_ajax', 'get_next_return_ajax_callback');

add_action('wp_ajax_get_deadline_ajax', 'get_deadline_ajax_callback');
add_action('wp_ajax_nopriv_get_deadline_ajax', 'get_deadline_ajax_callback');

add_action('wp_ajax_get_achieved_ajax', 'get_achieved_ajax_callback');
add_action('wp_ajax_nopriv_get_achieved_ajax', 'get_achieved_ajax_callback');

add_action('wp_ajax_get_maintenance_ajax', 'get_maintenance_ajax_callback');
add_action('wp_ajax_nopriv_get_maintenance_ajax', 'get_maintenance_ajax_callback');

add_action('wp_ajax_get_targa_ajax', 'get_targa_ajax_callback');
add_action('wp_ajax_nopriv_get_targa_ajax', 'get_targa_ajax_callback');

add_action('wp_ajax_save_deadline_ajax', 'save_deadline_ajax_callback');
add_action('wp_ajax_nopriv_save_deadline_ajax', 'save_deadline_ajax_callback');

add_action('wp_ajax_delete_deadline_ajax', 'delete_deadline_ajax_callback');
add_action('wp_ajax_nopriv_delete_deadline_ajax', 'delete_deadline_ajax_callback');

add_action('wp_ajax_save_maintenance_ajax', 'save_maintenance_ajax_callback');
add_action('wp_ajax_nopriv_save_maintenance_ajax', 'save_maintenance_ajax_callback');

add_action('wp_ajax_delete_maintenance_ajax', 'delete_maintenance_ajax_callback');
add_action('wp_ajax_nopriv_delete_maintenance_ajax', 'delete_maintenance_ajax_callback');

add_action('wp_ajax_search_targa_ajax', 'search_targa_ajax_callback');
add_action('wp_ajax_nopriv_search_targa_ajax', 'search_targa_ajax_callback');

add_action('wp_ajax_order_detail_ajax', 'order_detail_ajax_callback');
add_action('wp_ajax_nopriv_order_detail_ajax', 'order_detail_ajax_callback');

function get_next_return_ajax_callback()
{
    global $conn;
    global $basic_query;
    $page_length = $_POST['page_length'];
    if ($page_length === 'all') {
        $query = $basic_query . ";";
    } else {
        $query = $basic_query . " limit {$page_length};";
    }
    $results = $conn->query($query);

    foreach ($results as $result) {
        echo '<tr>';
        echo '<td>' . $result['id'] . '</td>';
        echo '<td>' . get_full_name($result['custdata']) . '</td>';
        echo '<td>' . $result['name'] . '</td>';
        echo '<td>' . date("d/m/y H.i", $result['consegna']) . '</td>';
        echo '</tr>';
    }

    wp_die();
}

function get_deadline_ajax_callback()
{
    global $conn2;
    $basic_query = "SELECT * FROM deadlines Order By scadenza DESC";
    $page_length = $_POST['page_length'];
    if ($page_length === 'all') {
        $query = $basic_query . ";";
    } else {
        $query = $basic_query . " limit {$page_length};";
    }
    $results = $conn2->query($query);

    foreach ($results as $result) {
        $date = new DateTime($result['scadenza']);
        echo '<tr>
        <td>' . '<input type="checkbox" data-id="' . $result['id'] . '">' . '</td>
        <td>' . $result['auto'] . '</td>
        <td>' . $result['targa'] . '</td>
        <td>' . $date->format('d/m/y') . '</td>
        <td>' . $result['descrizione'] . '</td>
        </tr>';
    }

    wp_die();
}

function get_achieved_ajax_callback()
{
    global $conn2;
    $basic_query = "SELECT * FROM intervento WHERE km > 10000";
    $page_length = $_POST['page_length'];
    if ($page_length === 'all') {
        $query = $basic_query . ";";
    } else {
        $query = $basic_query . " limit {$page_length};";
    }
    $results = $conn2->query($query);

    foreach ($results as $result) {
        echo '<tr>
            <td>' . $result['auto'] . '</td>
            <td>' . $result['targa'] . '</td>
            <td>' . $result['km'] . '</td>
        </tr>';
    }

    wp_die();
}

function get_maintenance_ajax_callback()
{
    global $conn2;
    $basic_query = "SELECT * FROM intervento";
    $page_length = $_POST['page_length'];
    if ($page_length === 'all') {
        $query = $basic_query . ";";
    } else {
        $query = $basic_query . " limit {$page_length};";
    }
    $results = $conn2->query($query);

    foreach ($results as $result) {
        echo '<tr>
            <td>' . '<input type="checkbox" data-id="' . $result['id'] . '">' . '</td>
            <td>' . $result['auto'] . '</td>
            <td>' . $result['targa'] . '</td>
            <td>' . $result['date'] . '</td>
            <td>' . $result['descrizione'] . '</td>
            <td>' . $result['km'] . '</td>
        </tr>';
    }

    wp_die();
}

function get_targa_ajax_callback()
{
    global $conn;
    $auto = $_POST['auto'];
    $results = $conn->query("SELECT params FROM wppe_vikrentcar_cars WHERE `name`='" . $auto . "';");
    // Get the first row of results
    $row = $results->fetch_assoc();
    $params = json_decode($row['params'], true);
    
    $targas = [];
    // Get the first row of results
    foreach ($params['features'] as $feature) {
        if (array_key_exists('Targa', $feature))
            array_push($targas, $feature['Targa']);
        if (array_key_exists('TARGA', $feature))
            array_push($targas, $feature['TARGA']);
    }

    asort($targas);
    asort($targas);
    foreach ($targas as $targa) {
        if($targa != '')
            echo '<option>' . $targa . '</option>';
    }

    wp_die();
}

function save_deadline_ajax_callback()
{
    global $conn2;
    $auto = $_POST['auto'];
    $targa = $_POST['targa'];
    $scadenza = $_POST['scadenza'];
    $description = $_POST['description'];

    $query = "INSERT INTO deadlines (`auto`, targa, scadenza, descrizione) VALUES('" . $auto . "', '" . $targa . "', '" . $scadenza . "', '" . $description . "');";
    $results = $conn2->query($query);

    wp_die();
}

function delete_deadline_ajax_callback()
{
    global $conn2;
    $ids = $_POST['ids'];
    $idsString = implode(',', $ids);
    $query = "DELETE FROM deadlines WHERE id IN ($idsString)";
    $conn2->query($query);

    wp_die();
}

function save_maintenance_ajax_callback()
{
    global $conn2;
    $auto = $_POST['auto'];
    $targa = $_POST['targa'];
    $date = $_POST['date'];
    $description = $_POST['description'];
    $km = $_POST['km'];

    $query = "INSERT INTO intervento (`auto`, targa, `date`, descrizione, km) VALUES('" . $auto . "', '" . $targa . "', '" . $date . "', '" . $description . "', '" . $km . "');";
    $results = $conn2->query($query);

    wp_die();
}

function delete_maintenance_ajax_callback()
{
    global $conn2;
    $ids = $_POST['ids'];
    $idsString = implode(',', $ids);
    $query = "DELETE FROM intervento WHERE id IN ($idsString)";
    $conn2->query($query);

    wp_die();
}

function search_targa_ajax_callback()
{
    global $conn2;
    $targa = $_POST['targa'];
    $query = "SELECT * FROM intervento WHERE targa='" . $targa . "'";
    $maintenances = $conn2->query($query)->fetch_all();
    
    $query = "SELECT * FROM deadlines WHERE targa='" . $targa . "'";
    $deadline = $conn2->query($query)->fetch_assoc();
    
    $result = array(
        'success' => true,
        'data' => array(
            'maintenances' => $maintenances,
            'deadline' => $deadline
        )
    );
    wp_send_json($result);
    wp_die();
}

function order_detail_ajax_callback()
{
    global $conn;
    $targa = $_POST['targa'];
    $query = "SELECT wppe_vikrentcar_orders.id, custdata, ritiro, consegna 
        FROM wppe_vikrentcar_orders LEFT JOIN wppe_vikrentcar_cars 
        ON wppe_vikrentcar_orders.idcar = wppe_vikrentcar_cars.id 
        WHERE wppe_vikrentcar_cars.params LIKE '%" . $targa . "%';";
    $results = $conn->query($query);
    
    foreach($results as $result) {
        $name = get_full_name($result['custdata']);
        $in = date("d/m/y H.i", $result['ritiro']);
        $out = date("d/m/y H.i", $result['consegna']);
        echo "<tr>
            <td>" . $result['id'] . "</td>
            <td>" . $name . "</td>
            <td>" . $in . "</td>
            <td>" . $out . "</td>
        </tr>";
    }
    wp_die();
}


// Connect to original DB
$servername = "89.40.172.236"; // Replace with the hostname of your MySQL server
$username = "thfcxywx_ciro"; // Replace with your MySQL username
$password = "Rompi_coglione1"; // Replace with your MySQL password
$dbname = "thfcxywx_clone"; // Replace with the name of your MySQL database

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset('utf8mb4');
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$basic_query = "select wppe_vikrentcar_orders.id, custdata, wppe_vikrentcar_cars.name, consegna, wppe_vikrentcar_orderhistory.type 
    from wppe_vikrentcar_orders 
    inner join wppe_vikrentcar_cars on wppe_vikrentcar_cars.id = wppe_vikrentcar_orders.idcar
    left join wppe_vikrentcar_orderhistory on wppe_vikrentcar_orders.id = wppe_vikrentcar_orderhistory.idorder and wppe_vikrentcar_orderhistory.type = 'RC'
    where wppe_vikrentcar_orderhistory.type is null
    order by consegna desc";

$conn2 = new mysqli($servername, $username, $password, "thfcxywx_Manutenzione"); //manutenzione
$conn2->set_charset('utf8mb4');
// Check connection
if ($conn2->connect_error) {
    die("Connection failed: " . $conn2->connect_error);
}

/* 
 * SCHEMA OF "INETVENTI" table
 * 
 * Auto: string
 * Targa: string
 * Date: date
 * Descrizione: string
 * Km: int
 * 
 * SCHEMA OF "DEADLINE" table
 * 
 * Auto: string
 * Targa: string
 * Scadenza: date
 * Descrizione: string
 */


//================================================================================================
// Main shortcode
function car_maintenance_shortcode()
{
    global $conn;
    global $conn2;
    global $basic_query;
    $script = "";

    $next_return = '<div id="next-return-div" class="col-12 col-lg-6 d-flex flex-column p-3">
    <div class="text-warning fs-5 d-flex justify-content-between">    
        <span>NEXT RETURNS</span>
        <div class="d-flex gap-3">
            <label class="d-flex align-items-center"><input class="me-3" type="radio" name="page_length" value="5" checked> 5</label>
            <label class="d-flex align-items-center"><input class="me-3" type="radio" name="page_length" value="10"> 10</label>
            <label class="d-flex align-items-center"><input class="me-3" type="radio" name="page_length" value="all"> All</label>
        </div>
        <div></div>
    </div>
    <div class="p-3 mt-3 overflow-hidden rounded-3 flex-grow-1 bg-pink">
        <table id="next_return_table" class="bg-transparent" style="border-spacing: 10px; width: 100%;">
            <thead>
                <tr>
                    <th>Order nr</th>
                    <th>Customer</th>
                    <th>Auto</th>
                    <th>Date return</th>
                </tr>
            </thead>
            <tbody>';

    // Initial query and pagination settings
    $page_length = 5;
    $query = $basic_query . " limit {$page_length};";
    $results = $conn->query($query);

    foreach ($results as $result) {
        $next_return .= '<tr>';
        $next_return .= '<td>' . $result['id'] . '</td>';
        $next_return .= '<td>' . get_full_name($result['custdata']) . '</td>';
        $next_return .= '<td>' . $result['name'] . '</td>';
        $next_return .= '<td>' . date("d/m/y H.i", $result['consegna']) . '</td>';
        $next_return .= '</tr>';
    }

    $next_return .= '</tbody>
                </table>
            </div>  
        </div>';

    $upcoming_deadline = '<div id="deadline-div" class="col-12 col-lg-6 d-flex flex-column p-3">
        <div class="text-warning fs-5 d-flex justify-content-between">    
            <span>UPCOMING DEADLINES</span>
            <div class="d-flex gap-3">
                <label class="d-flex align-items-center"><input class="me-3" type="radio" name="page_length1" value="5" checked> 5</label>
                <label class="d-flex align-items-center"><input class="me-3" type="radio" name="page_length1" value="10"> 10</label>
                <label class="d-flex align-items-center"><input class="me-3" type="radio" name="page_length1" value="all"> All</label>
            </div>
            <div></div>
        </div>
        <div class="p-3 mt-3 overflow-hidden rounded-3 flex-grow-1 bg-pink">
            <table id="deadline_table" class="bg-transparent" style="border-spacing: 10px; width: 100%;">
                <thead>
                    <tr>
                        <th></th>
                        <th>Auto</th>
                        <th>Targa</th>
                        <th>Scadenza</th>
                        <th>Descrizione</th>
                    </tr>
                </thead>
                <tbody>';
    
    $results = $conn2->query("select * from deadlines;");
    foreach ($results as $result) {
        $date = new DateTime($result['scadenza']);
        $upcoming_deadline .= '<tr>
        <td>' . '<input type="checkbox" data-id="' . $result['id'] . '">' . '</td>
        <td>' . $result['auto'] . '</td>
        <td>' . $result['targa'] . '</td>
        <td>' . $date->format('d/m/y') . '</td>
        <td>' . $result['descrizione'] . '</td>
        </tr>';
    }

    $upcoming_deadline .= '
                </tbody>
            </table>
            <div class="d-flex justify-content-center mt-3">
                <button class="btn-add me-2" data-bs-toggle="modal" data-bs-target="#deadline-create-modal">Add new</button>
                <button class="btn-delete-deadline">Delete</button>
            </div>
        </div>
    </div>';


    $results = $conn->query("select params from wppe_vikrentcar_cars;");
    $targas = [];
    // Get the first row of results
    foreach($results as $result) {
        $params = json_decode($result['params'], true);
        foreach ($params['features'] as $feature) {
            if (array_key_exists('Targa', $feature))
                array_push($targas, $feature['Targa']);
            if (array_key_exists('TARGA', $feature))
                array_push($targas, $feature['TARGA']);
        }
    }

    asort($targas);
    $options = '';
    foreach ($targas as $targa) {
        $options .= '<option>' . $targa . '</option>';
    }

    $search_targa = '<div class="col-12 col-lg-6 d-flex flex-column p-3">
        <div class="text-danger fs-5">    
            <span>Search TARGA:</span>
            <select id="search-targa-select" class="fs-5">
                <option disabled selected>- Please Select -</option>'. $options .
            '</select>
        </div>
        <div class="p-3 mt-3 overflow-hidden rounded-3 flex-grow-1 bg-azure">
            <div class="mb-3">
                <span class="me-2">Modello: <strong class="search-auto"></strong></span>
                <span class="me-2">Targa: <strong class="search-targa"></strong></span>
                <span class="me-2">Km: <strong class="search-km"></strong></span>
            </div>
            <table id="targa_search_table" class="bg-transparent" style="border-spacing: 10px; width: 100%;">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Km</th>
                        <th>Deadline</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div class="d-flex justify-content-center mt-3">
                <button class="btn-order-detail">See Orders</button>
            </div>
        </div>  
    </div>';

    $kilometer_achieve = '<div id="achieved-div" class="col-12 col-lg-6 d-flex flex-column p-3">
        <div class="text-danger fs-5 d-flex justify-content-between">    
            <span>Kilometers achieved(each 10,000km)</span>
            <div class="d-flex gap-3">
                <label class="d-flex align-items-center"><input class="me-3" type="radio" name="page_length2" value="5" checked> 5</label>
                <label class="d-flex align-items-center"><input class="me-3" type="radio" name="page_length2" value="10"> 10</label>
                <label class="d-flex align-items-center"><input class="me-3" type="radio" name="page_length2" value="all"> All</label>
            </div>
            <div></div>
        </div>
        <div class="p-3 mt-3 overflow-hidden rounded-3 flex-grow-1 bg-azure">
            <table id="achieved_table" class="bg-transparent" style="border-spacing: 10px; width: 100%;">
                <thead>
                    <tr>
                        <th>Auto</th>
                        <th>Targa</th>
                        <th>KM</th>
                    </tr>
                </thead>
                <tbody>';
                    
    $results = $conn2->query("select * from intervento where km > 10000;");
    foreach ($results as $result) {
        $kilometer_achieve .= '<tr>
            <td>' . $result['auto'] . '</td>
            <td>' . $result['targa'] . '</td>
            <td>' . $result['km'] . '</td>
        </tr>';
    }

    $kilometer_achieve .= '
                </tbody>
            </table>
        </div>  
    </div>';

    $maintenance_list = '<div id="maintenance-div" class="col-12 p-3">
        <div class="text-white fs-5">    
            <span>Maintenace List(each 10,000km)</span>
            <div class="d-flex gap-3">
                <label class="d-flex align-items-center"><input class="me-3" type="radio" name="page_length3" value="5" checked> 5</label>
                <label class="d-flex align-items-center"><input class="me-3" type="radio" name="page_length3" value="10"> 10</label>
                <label class="d-flex align-items-center"><input class="me-3" type="radio" name="page_length3" value="all"> All</label>
            </div>
            <div></div>
        </div>
        <div class="p-3 mt-3 overflow-hidden rounded-3 flex-grow-1 bg-warning">
            <table id="maintenance_table" class="bg-transparent" style="border-spacing: 10px; width: 100%;">
                <thead>
                    <tr>
                        <th></th>
                        <th>Auto</th>
                        <th>Targa</th>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Km</th>
                    </tr>
                </thead>
                <tbody>';
                    
    $results = $conn2->query("select * from intervento;");
    foreach ($results as $result) {
        $maintenance_list .= '<tr>
            <td>' . '<input type="checkbox" data-id="' . $result['id'] . '">' . '</td>
            <td>' . $result['auto'] . '</td>
            <td>' . $result['targa'] . '</td>
            <td>' . $result['date'] . '</td>
            <td>' . $result['descrizione'] . '</td>
            <td>' . $result['km'] . '</td>
        </tr>';
    }
            
    $maintenance_list .= '</tbody>
            </table>
            <div class="d-flex justify-content-center mt-3">
                <button class="btn-add me-2" data-bs-toggle="modal" data-bs-target="#maintenance-create-modal">Add new</button>
                <button class="btn-delete-maintenance">Delete</button>
            </div>
        </div>  
    </div>';

    $results = $conn->query("select name from wppe_vikrentcar_cars;");
    $car_options = '';
    foreach ($results as $result) {
        $car_options .='<option>' . $result['name'] . '</option>';
    }
    $modal = '<div class="modal" id="deadline-create-modal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add new deadline</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Auto</label>
                        <select class="form-select auto-select">'
                        . $car_options .
                        '</select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Targa</label>
                        <select class="form-select targa-select">
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Scadenza</label>
                        <input type="date" class="form-control scadenza-input"/>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Descrizione</label>
                        <input type="text" class="form-control description-input"/>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-save-deadline">Save</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="maintenance-create-modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add new maintenance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Auto</label>
                        <select class="form-select auto-select">'
                        . $car_options .
                        '</select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Targa</label>
                        <select class="form-select targa-select">
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date</label>
                        <input type="date" class="form-control date-input"/>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Descrizione</label>
                        <input type="text" class="form-control description-input"/>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Km</label>
                        <input type="number" class="form-control km-input"/>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-save-maintenance">Save</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="oder-detail-modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <span class="me-2">Modello: <strong class="search-auto"></strong></span>
                        <span class="me-2">Targa: <strong class="search-targa"></strong></span>
                        <span class="me-2">Km: <strong class="search-km"></strong></span>
                    </div>
                    <table id="order-detail-table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>In</th>
                                <th>Out</th>
                            <tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>';
    
    // AJAX functions to fetch from db and write to db
    $script .= "<script>
        jQuery(document).ready(function($) {
            // get next returns
            $('#next-return-div input[name=page_length]').change(function() {
                var page_length = $(this).val();
                var data = {
                    'action': 'get_next_return_ajax',
                    'page_length': page_length
                };
                $('#loading-spinner').show();
                $.post('/wp-admin/admin-ajax.php', data, function(response) {
                    $('#next_return_table tbody').html(response);
                    $('#loading-spinner').hide();
                });
            });

            // get deadline
            $('#deadline-div input[name=page_length1]').change(function() {
                var page_length = $(this).val();
                var data = {
                    'action': 'get_deadline_ajax',
                    'page_length': page_length
                };
                $('#loading-spinner').show();
                $.post('/wp-admin/admin-ajax.php', data, function(response) {
                    $('#deadline_table tbody').html(response);
                    $('#loading-spinner').hide();
                });
            });

            // get achieved
            $('#achieved-div input[name=page_length2]').change(function() {
                var page_length = $(this).val();
                var data = {
                    'action': 'get_achieved_ajax',
                    'page_length': page_length
                };
                $('#loading-spinner').show();
                $.post('/wp-admin/admin-ajax.php', data, function(response) {
                    $('#archieved_table tbody').html(response);
                    $('#loading-spinner').hide();
                });
            });

            // get maintenance
            $('#maintenance-div input[name=page_length3]').change(function() {
                var page_length = $(this).val();
                var data = {
                    'action': 'get_maintenance_ajax',
                    'page_length': page_length
                };
                $('#loading-spinner').show();
                $.post('/wp-admin/admin-ajax.php', data, function(response) {
                    $('#maintenance_table tbody').html(response);
                    $('#loading-spinner').hide();
                });
            });

            // get search result
            $('#search-targa-select').change(function(e) {
                var targa = $(e.currentTarget).val();
                var data = {
                    'action': 'search_targa_ajax',
                    'targa': targa
                };
                $('#loading-spinner').show();
                $.post('/wp-admin/admin-ajax.php', data, function(response) {
                    console.log(response);
                    if(response['success'] != true)
                    {
                        alert('Failed to get data');
                        $('#loading-spinner').hide();
                        return;
                    }

                    var maintenances = response['data']['maintenances'];
                    if(maintenances.length == 0) {
                        alert('Not found');
                        $('#loading-spinner').hide();
                        return;
                    }
                    var deadline = response['data']['deadline'];

                    $('.search-auto').text(maintenances.at(-1)[1]);
                    $('.search-targa').text(targa);
                    $('.search-km').text(maintenances.at(-1)[5]);

                    var trs = '';
                    maintenances.forEach(function(item) {
                        trs += '<tr>\
                            <td>' + item[3] + '</td>\
                            <td>' + item[4] + '</td>\
                            <td>' + item[5] + '</td>\
                            <td></td>\
                        </tr>';
                    });

                    if(deadline) {
                        trs += '<tr>\
                            <td></td>\
                            <td>' + deadline['descrizione'] + '</td>\
                            <td></td>\
                            <td>' + deadline['scadenza'] + '</td>\
                        </tr>';
                    }
                        
                    
                    $('#targa_search_table tbody').html(trs);
                    $('#loading-spinner').hide();
                });
            });

            // update targa list when car is choosen
            $('.auto-select').change(function(e) {
                var auto = $(e.currentTarget).val();
                var data = {
                    'action': 'get_targa_ajax',
                    'auto': auto
                };
                $('#loading-spinner').show();
                $.post('/wp-admin/admin-ajax.php', data, function(response) {
                    $('.targa-select').html(response);
                    $('#loading-spinner').hide();
                });
            });

            // add new deadline
            $('.btn-save-deadline').click(function(e) {
                var auto = $(e.currentTarget).val();
                var data = {
                    'action': 'save_deadline_ajax',
                    'auto': $('#deadline-create-modal .auto-select').val(),
                    'targa': $('#deadline-create-modal .targa-select').val(),
                    'scadenza': $('#deadline-create-modal .scadenza-input').val(),
                    'description': $('#deadline-create-modal .description-input').val(),
                };
                $('#loading-spinner').show();
                $.post('/wp-admin/admin-ajax.php', data, function(response) {
                    location.reload();
                });
            });

            // delete deadline
            $('.btn-delete-deadline').click(function(e) {
                var ids = [];
                $('#deadline_table input[type=\"checkbox\"]:checked').each(function() {
                    ids.push($(this).data('id'));
                });
                var data = {
                    'action': 'delete_deadline_ajax',
                    'ids': ids
                };
                $('#loading-spinner').show();
                $.post('/wp-admin/admin-ajax.php', data, function(response) {
                    location.reload();
                });
                console.log(ids);
            });

            // add new maintenance
            $('.btn-save-maintenance').click(function(e) {
                var auto = $(e.currentTarget).val();
                var data = {
                    'action': 'save_maintenance_ajax',
                    'auto': $('#maintenance-create-modal .auto-select').val(),
                    'targa': $('#maintenance-create-modal .targa-select').val(),
                    'date': $('#maintenance-create-modal .date-input').val(),
                    'description': $('#maintenance-create-modal .description-input').val(),
                    'km': $('#maintenance-create-modal .km-input').val(),
                };
                $('#loading-spinner').show();
                $.post('/wp-admin/admin-ajax.php', data, function(response) {
                    location.reload();
                });
            });

            // delete maintenance
            $('.btn-delete-maintenance').click(function(e) {
                var ids = [];
                $('#maintenance_table input[type=\"checkbox\"]:checked').each(function() {
                    ids.push($(this).data('id'));
                });
                var data = {
                    'action': 'delete_maintenance_ajax',
                    'ids': ids
                };
                $('#loading-spinner').show();
                $.post('/wp-admin/admin-ajax.php', data, function(response) {
                    location.reload();
                });
                console.log(ids);
            });

            // get order detail
            $('.btn-order-detail').click(function(e) {
                var targa = $('#search-targa-select').val();
                if(targa == null) {
                    alert('Please select targa');
                    return;
                }
                var data = {
                    'action': 'order_detail_ajax',
                    'targa': targa
                };
                $('#loading-spinner').show();
                $.post('/wp-admin/admin-ajax.php', data, function(response) {
                    if(response=='')
                    {
                        alert('no orders');
                        $('#loading-spinner').hide();
                        return;
                    }
                    $('#order-detail-table tbody').html(response);
                    $('#oder-detail-modal').modal('show');
                    $('#loading-spinner').hide();
                });
            });
        });
    </script>";

    $output = '<div class="card wp-block">
        <div class="card-body position-relative d-flex flex-wrap p-10 rounded-2 bg-dark-blue">
            <div id="loading-spinner"></div>'
            . $next_return . $upcoming_deadline . $search_targa . $kilometer_achieve . $maintenance_list .
        '</div>' . $modal .
    '</div>'. $script;


    return $output;
}
add_shortcode('car_maintenance', 'car_maintenance_shortcode');

// Utiliies
function get_full_name($custdata)
{
    $full_name = '';
    if (preg_match('/Nome:\s*(\w+)\s*\nCognome:\s*(\w+)/', $custdata, $matches)) {
        $full_name = ucfirst(strtolower($matches[1])) . ' ' . ucfirst(strtolower($matches[2]));
    }
    return $full_name;
}
