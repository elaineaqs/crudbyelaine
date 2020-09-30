<?php 

$jsonFile = "groceryItems.json";
$addItem = $updateItem = $deleteItem = "";
$addId = $updateId = $deleteId = 0;
$index = 0;

/* Check for nonItemCategories and add default if none */
function checkNonItemCategories() {
    $list = file_get_contents($GLOBALS['jsonFile']);
    $list = json_decode($list);
    
    if(!is_array($list)) {
        $add_default_category = array(
            'id' => 0,
            'nonItemCategories' => [ "General Category" ]
        );
    
        $list[] = $add_default_category;

        // Encode as JSON
        $list = json_encode($list, JSON_PRETTY_PRINT);

        // Append to JSON file, return true, and refresh
        if (file_put_contents($GLOBALS['jsonFile'], $list)) {
            return true;
        }   
    }
}

checkNonItemCategories();

/* Current index (total items) */
function currentIndex($i) {
    $list = file_get_contents($GLOBALS['jsonFile']);
    $list = json_decode($list);
                    
    // Check file if empty
    if(!empty(readItem())){

        // Not empty? Read file data
        foreach($list as $row) {
            $i++; 
        }
        return $i-1;
        header('location: index.php');
    }

    // Empty? Put 0
    else {
        return 0;
        header('location: index.php');
    }
}

/* Test form inputs */
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = preg_replace('/[^A-Za-z0-9\-]/', ' ', $data); // Removes special chars
    return $data;
}

/* Create a new item and return true */
function addItem(int $id, $itemName, $itemCategory) {
    $list = file_get_contents($GLOBALS['jsonFile']);
    $list = json_decode($list, true);

    // Add 1
    $add_item = array(
            'id' => $id,
            'name' => $itemName,
            'category' => $itemCategory
        );

    // Add item
    array_push($list, $add_item);

    // Encode as JSON
    $list = json_encode($list, JSON_PRETTY_PRINT);

    // Append to JSON file, return true, and refresh
    file_put_contents($GLOBALS['jsonFile'], $list);
    return true;
    header('location: index.php');
    
}

/* Read contents of JSON File, decode, and return contents*/
function readItem() {
    $list = file_get_contents($GLOBALS['jsonFile']);
    $list = json_decode($list);
    return $list;
}

/* Update an item and return true */
function updateItem(int $id, $newName, $newCategory) {
    $list = file_get_contents($GLOBALS['jsonFile']);
    $list = json_decode($list, true);

    // Update row with new name
    /*$list[$id]["id"] = $id;
    $list[$id]["name"] = $newName;
    $list[$id]["category"] = $newCategory;*/
    

    for($x = 0; $x < count($list); $x++) {
        if($list[$x]["id"] == $id) {
            $list[$x]["name"] = $newName;
            $list[$x]["category"] = $newCategory;
        }
    }

    // Encode as JSON
    $list = json_encode($list, JSON_PRETTY_PRINT);

    // Update JSON file, return true, and refresh
    if (file_put_contents($GLOBALS['jsonFile'], $list)) {
        return true;
        header('location: index.php');
    }
}

/* Delete an item and return true */
function deleteItem(int $id) {
    $list = file_get_contents($GLOBALS['jsonFile']);
    $list = json_decode($list, true);

    foreach ($list as $i => $item) {
        if ($item["id"] == $id) {
            array_splice($list, $i, 1);
        }
    }

    // Encode as JSON
    $list = json_encode($list, JSON_PRETTY_PRINT);

    // Update JSON file, return true, and refresh
    file_put_contents($GLOBALS['jsonFile'], $list);
    return true;
    header('location: index.php');
}

/* Read categories and filter out blank values */
function getCat() {
    $list = file_get_contents($GLOBALS['jsonFile']);
    $list = json_decode($list, true);

    // Non item categories
    $nonItemCategories = $list[0]["nonItemCategories"];

    // Check if items with categories exist
    if (!empty($itemCategories)) {
        $itemCategories = array_column($list, 'category');
        $itemCategories = array_unique($itemCategories);        
        
        // Add item and non-item categories together
        $allCategories = array_merge($nonItemCategories, $itemCategories);
        $allCategories = array_unique($allCategories);
    }
    else {
        $allCategories = $nonItemCategories;
    }

    return array_filter($allCategories);
}

/* Add category,  Return true if added, false if otherwise */
function addCat($category, $all) {

    // Check if there's more than one category since array_search cannot return the first instance
    $x = array_search($category, $all, true);

    // First existing category
    if ($x == 0 && $x !== false) {
        return false;
    }
    // Other existing category
    elseif ($x > 0) {
        return false;
    }
    // No category found, add one
    else {
        array_push($all, $category);
        
        // Add to JSON file
        $list = file_get_contents($GLOBALS['jsonFile']);
        $list = json_decode($list, true);

        // Update nonItemCategories with new category name
        $list[0]["nonItemCategories"] = $all;

        // Encode as JSON
        $list = json_encode($list, JSON_PRETTY_PRINT);

        // Update JSON file, return true, and refresh
        file_put_contents($GLOBALS['jsonFile'], $list);
        return true;
        //header('location: index.php?categoryView');
        
    }

}

/* Update category */
function updateCat($oldCategory, $newCategory, $all) {
    $list = file_get_contents($GLOBALS['jsonFile']);
    $list = json_decode($list, true);

    // Update category name
    foreach($all as $category => $value) {
        if ($value == $oldCategory) {
            $list[0]["nonItemCategories"][$category] = $newCategory;
        }
    }

    // Encode as JSON
    $list = json_encode($list, JSON_PRETTY_PRINT);

    // Update JSON file, return true, and refresh
    file_put_contents($GLOBALS['jsonFile'], $list);
    return true;
    //header('location: index.php?categoryView');
    
}

/* Delete a category or set to blank */
function deleteCat($deleteCategory, $all) {
    $list = file_get_contents($GLOBALS['jsonFile']);
    $list = json_decode($list, true);

    // Set category name to blank
    foreach($all as $category => $value) {
        if ($value == $deleteCategory) {
            $list[0]["nonItemCategories"][$category] = "";
        }
    }

    // Remove blank categories
    $cleanCategories = array_filter($list[0]["nonItemCategories"]);

    // Add cleaned categories to first item in array
    $list[0]["nonItemCategories"] = $cleanCategories;

    // Encode as JSON
    $list = json_encode($list, JSON_PRETTY_PRINT);

    // Update JSON file, return true, and refresh
    file_put_contents($GLOBALS['jsonFile'], $list);
    return true;
    //header('location: index.php?categoryView');
        
}

?><!DOCTYPE html>
  <head>
    <title>CRUD by Elaine</title>
    <link rel="stylesheet" href="./assets/css/bulma.min.css">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        .level-item {
            padding: 1rem 2rem;
        }
        .buttons {
            padding: 4px 11px;
        }
        .button {
            padding: 10px;
        }
        .modal-card {
            padding: 20px 10px;
        }
        .notification:not(:last-child) {
            margin: 0;
        }
        .center {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        body {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }
    
        #wrapper {
            flex: 1;
        }
    </style>
  </head>

  <body>

  <div id="wrapper">

<?php 

if ($_SERVER["REQUEST_METHOD"] == "GET" || $_SERVER["REQUEST_METHOD"] == "POST") {

                // Add Category
                if(isset($_POST["addCategory"])) {
                    $addCategory = test_input($_POST["addCategory"]);
                    $currentCat = getCat();
                    $checkCat = array_search($addCategory, $currentCat);

                    if($checkCat > 0) {
                        $message = "<div class=\"notification is-warning is-light has-text-centered\"><button class=\"delete\"></button><strong>$addCategory</strong> already exists.</div>";
                    }
                    else {
                        // Add a category
                        if(addCat($addCategory, getCat())) {
                            $message = "<div class=\"notification is-success is-light has-text-centered\"><button class=\"delete\"></button>Added <strong>$addCategory</strong>.</div>";
                        }                        
                    }                    
                }

                // Update Category
                elseif(isset($_POST["updateCategory"])) {
                    $updateCategory = test_input($_POST["updateCategory"]);
                    $oldCategory = test_input($_POST["oldCategory"]);
                    $currentCat = getCat();
                    $checkCat = array_search($updateCategory, $currentCat);
                    
                    if($checkCat > 0) {
                        $message = "<div class=\"notification is-warning is-light has-text-centered\"><button class=\"delete\"></button><strong>$updateCategory</strong> already exists.</div>";
                    }
                    else {
                        // Update category
                        if(updateCat($oldCategory, $updateCategory, getCat())) {
                            $message = "<div class=\"notification is-info is-light has-text-centered\"><button class=\"delete\"></button>Updated category from <strong>$oldCategory</strong> to <strong>$updateCategory</strong>.</div>";
                        }
                    }
                }

                // Delete Category
                elseif(isset($_POST["deleteCategory"])) {
                    $deleteCategory = test_input($_POST["deleteCategory"]);

                    if(deleteCat($deleteCategory, getCat())) {
                        $message = "<div class=\"notification is-danger is-light has-text-centered\"><button class=\"delete\"></button>Deleted category <strong>$deleteCategory</strong>.</div>";
                    }
                }

    // Categories
    if(isset($_GET["categoryView"])) {
        $categoryView = test_input($_GET["categoryView"]);

        echo '    <!-- Modal -->
        <div class="modal is-active is-clipped" id="updateCatModal">
            <div class="modal-background"></div>
            <div class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">Update Categories</p>
                    <button class="delete" aria-label="close" onclick="location.replace(\'index.php\')"></button>
                </header>
                    <section class="modal-card-body">
                        ' . $message . '
                            <nav class="level">
                                <div class="level-left">
                                
                                        <div class="level-item has-text-left">
                                            <ul>'; 
                                            
                                                $categories = getCat();
                                                foreach($categories as $category) {
                                                    if($category !== "") {
                                                        echo '<li>
                                                            <div class="field is-grouped">
                                                                <p class="control">
                                                                    <form class="container level-right" method="POST" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '?categoryView">
                                                                        <input type="text" class="button is-small is-outlined has-text-left" name="updateCategory" value="' . $category . '" required>
                                                                        <input type="hidden" name="oldCategory" value="' . $category . '">
                                                                    </form>
                                                                </p>
                                                                <p class="control">
                                                                    <form method="POST" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '?categoryView">
                                                                        <input type="hidden" name="deleteCategory" value="' . $category . '">';
                                                                        if (count($categories) <= 1) {
                                                                            echo '';
                                                                        }
                                                                        else {
                                                                            echo '<button class="button is-small is-danger is-outlined" type="submit">X</button>
                                                                    </form>
                                                                </p>
                                                            </div>
                                                        </li>';
                                                                        }
                                                    }
                                                }

                    echo '                  </ul>
                                        </div>
                                
                                </div>
                                <div class="field has-addons level-item has-text-centered">
                                    <form class="container level-right" method="POST" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '?categoryView">
                                        <p class="control">
                                            <input type="text" class="input is-small" placeholder="Category Name" name="addCategory" autofocus>
                                        </p>
                                        <p class="control">
                                            <button class="button is-small is-success" type="submit">Add</button>
                                        </p>
                                    </form>
                                </div>
                            </nav>
                    </section>
                    <footer class="modal-card-foot center">
                        <p class="help">Please press ENTER to save your changes.</p>
                    </footer>                      
                    
            </div>
        </div>
        ';

    }

}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Create
    if(isset($_POST["addItem"]) && isset($_POST["addId"]) && isset($_POST["addCat"])) {
            $addItem = test_input($_POST["addItem"]);
            $addId = test_input($_POST["addId"]) + 1;
            $addCat = test_input($_POST["addCat"]);
    
            if(addItem($addId, $addItem, $addCat)) {
                echo "<div class=\"notification is-success is-light has-text-centered\"><button class=\"delete\"></button>Added <strong>$addItem</strong> under <strong>$addCat</strong>.</div>";
            }
    }

    // Update
    elseif(isset($_POST["updateItem"]) && isset($_POST["updateId"]) && isset($_POST["updateCat"])) {
            $updateItem = test_input($_POST["updateItem"]);
            $updateId = test_input($_POST["updateId"]);
            $updateCat = test_input($_POST["updateCat"]);
            
    
            if(updateItem($updateId, $updateItem, $updateCat)) {
                echo "<div class=\"notification is-success is-light has-text-centered\"><button class=\"delete\"></button>Updated to <strong>$updateItem</strong> under <strong>$updateCat</strong>.</div>";
            }
    }

    // Delete
    elseif(isset($_POST["deleteItem"]) && isset($_POST["deleteId"])) {
            $deleteItem = test_input($_POST["deleteItem"]);
            $deleteId = test_input($_POST["deleteId"]);
            
            if(deleteItem($deleteId)) {
                echo "<div class=\"notification is-success is-light has-text-centered\"><button class=\"delete\"></button><strong>$deleteItem</strong> deleted.</div>";
            }
    }

}

?>

    <header class="hero is-link is-small" style="margin-bottom: 40px;">
        <div class="hero-body">
            <div class="container has-text-centered">
                <h1 class="title is-3">Your Grocery List</h1>
                <h2 class="subtitle is-5">A simple way to shop.</h2>
                <nav>
                    <a href="index.php" class="button is-small is-warning">HOME</a>
                    <a href="?categoryView" onclick="toggle_visibility('updateCatModal')" class="button is-small is-warning toggle">CATEGORIES</a>
                </nav>
            </div>
        </div>
    </header>

    <nav class="container" style="margin-bottom: 20px;">
        <label class="help has-text-centered" style="padding-bottom: 10px;">Search items by category</label>
        <form method="GET" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="field has-addons has-addons-centered" style="padding-bottom: 20px;">
                <p class="control">
                    <select name="filterByCategory" class="select is-light is-small">
                    <?php
                        $categories = getCat();                
                        
                        if(isset($_GET["filterByCategory"]) && ($_GET["filterByCategory"] !== "")) {
                            $filterByCategory = test_input($_GET["filterByCategory"]);

                            foreach($categories as $category) {
                                if($category !== "") {
                                    if($filterByCategory == $category) {
                                        echo '<option selected value="' . $category . '">' . $category . '</option>';
                                    }
                                    else {
                                        echo '<option value="' . $category . '">' . $category . '</option>';
                                    }
                                }
                            }
                        }
                        else {
                            foreach($categories as $category) {
                                if($category !== "") {
                                    echo '<option value="' . $category . '">' . $category . '</option>';
                                }
                            }
                        }
                    ?>
                    </select>
                </p>
                <p class="control">
                    <button class="button is-small is-dark" type="submit">Search</button>
                </p>
            </div>
        </form>
    </nav>
    
    <section class="columns">
        <div class="column is-full-desktop has-text-centered">
            
            <?php 
                // Variable for data; get from readItem function
                $dataFilter = readItem();
                $categories = getCat();

                // Check file if empty
                if(!empty(readItem())){

                    // Check filter view
                    if(isset($_GET["filterByCategory"]) && ($_GET["filterByCategory"] !== "")) {
                        $filterByCategory = test_input($_GET["filterByCategory"]);
                        $itemCategoryCount = array_count_values(array_column($dataFilter, 'category'));

                        if (isset($itemCategoryCount[$filterByCategory])) {
                            $filterCount = $itemCategoryCount[$filterByCategory];
                            
                            if (array_key_exists($filterByCategory, $itemCategoryCount)) { 
                                echo '<h2 class="title is-5">Items Total for '. $filterByCategory . ': ' . $filterCount . '</h2>';
                            }

                        }
                        else {
                            echo '<h2 class="title is-5">No items for '. $filterByCategory . '</h2>';
                        }
                        
                        // List all rows
                        foreach ($dataFilter as $row) {

                            if (isset($row->category)) {
                                
                                // Filter by category
                                if ($filterByCategory == $row->category) {
                                    if ($filterCount > 0) {
                                            echo '
                                            <div class="field is-grouped is-grouped-centered">
                                                <p class="control">
                                                    <form method="POST" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">
                                                        <input type="text" class="button is-small is-outlined has-text-left" name="updateItem" required value="' . $row->name . '">
                                                        <input type="hidden" name="updateId" value="' .  $row->id . '">
                                                        <div class="select is-light is-small">
                                                            <select name="updateCat">
                                                                ';

                                                            
                                                                if ($row->category == $filterByCategory) {
                                                                    echo '<option value="' . $row->category . '" selected>' . $row->category . '</option>';
                                                                }
                                                                else {
                                                                    echo '<option value="' . $row->category . '">' . $row->category . '</option>';
                                                                }
                                                            

                                                    echo '    </select>
                                                        </div>
                                                        <button class="button is-small is-info" type="submit">Save changes</button>
                                                    </form>
                                                </p>
                                                <p class="control">
                                                    <form method="POST" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">
                                                        <input type="hidden" name="deleteItem" value="' . $row->name . '">
                                                        <input type="hidden" name="deleteId" value="' .  $row->id . '">
                                                        <button class="button is-small is-danger is-outlined" type="submit">X</button>
                                                    </form>
                                                </p>
                                            </div>
                                            ';
                                    }
                                }
                            }
                        }


                    }
                    else {
                        echo '<h2 class="title is-5">Items Total: ' . currentIndex($GLOBALS['index']) . '</h2>';
                        foreach($dataFilter as $row) {
                            if($row->id > 0) {
                                echo '
                                <div class="field is-grouped is-grouped-centered">
                                    <p class="control">
                                        <form method="POST" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">
                                            <input type="text" class="button is-small is-outlined has-text-left" name="updateItem" required value="' . $row->name . '">
                                            <input type="hidden" name="updateId" value="' .  $row->id . '">
                                            <div class="select is-light is-small">
                                                <select name="updateCat">
                                                    ';

                                                foreach ($categories as $category) {
                                                    if ($row->category == $category) {
                                                        echo '<option value="' . $row->category . '" selected>' . $row->category . '</option>';
                                                    }
                                                    else {
                                                        echo '<option value="' . $category . '">' . $category . '</option>';
                                                    }
                                                }

                                        echo '    </select>
                                            </div>
                                            <button class="button is-small is-info" type="submit">Save changes</button>
                                        </form>
                                    </p>
                                    <p class="control">
                                        <form method="POST" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">
                                            <input type="hidden" name="deleteItem" value="' . $row->name . '">
                                            <input type="hidden" name="deleteId" value="' .  $row->id . '">
                                            <button class="button is-small is-danger is-outlined" type="submit">X</button>
                                        </form>
                                    </p>
                                </div>
                                ';
                            }
                        }                        
                    }
                }
                // Empty? Echo this
                else {
                    echo "No items.";
                }
            ?>

        </div>
    </section>

                <!-- Add item form -->
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                    <div class="field has-addons has-addons-centered" style="padding-bottom: 20px;">
                        <p class="control">
                            <input type="text" class="input is-small is-primary has-text-left" placeholder="Add an item" name="addItem" required>
                        </p>
                        <p class="control">
                            <select name="addCat" class="select is-primary is-small">
                                <?php
                                    $categories = getCat();
                                    
                                    foreach($categories as $category) { 
                                        echo '<option value=" ' . $category . '">' . $category . '</option>';
                                    }
                                ?>
                            </select>
                        </p>
                        <p class="control">
                            <input type="hidden" name="addId" value="<?php echo currentIndex($GLOBALS['index']); ?>" required>
                            <button class="button is-success is-small" type="submit">Add</button>
                        </p>
                    </div>
                </form>
</div>
    
    <footer class="footer" style="margin-top: 40px;">
        <p class="content has-text-centered">
            &copy; 2020 CRUD by Elaine
        </p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
        (document.querySelectorAll('.notification .delete') || []).forEach(($delete) => {
            $notification = $delete.parentNode;

            $delete.addEventListener('click', () => {
            $notification.parentNode.removeChild($notification);
            });
        });
        });
    </script>
</body>
</html>