<?php 

$jsonFile = "groceryItems.json";
$addItem = $updateItem = $deleteItem = "";
$addId = $updateId = $deleteId = 0;
$index = 0;

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
        return $i;
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
    $list[] = $add_item;

    // Encode as JSON
    $list = json_encode($list, JSON_PRETTY_PRINT);

    // Append to JSON file, return true, and refresh
    if (file_put_contents($GLOBALS['jsonFile'], $list)) {
        return true;
        header('location: index.php');
    }
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

    $id--;

    // Update row with new name
    $list[$id]["id"] = $id;
    $list[$id]["name"] = $newName;
    $list[$id]["category"] = $newCategory;

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

/* Add category to items */
function addCat(int $id, $category) {
    $list = file_get_contents($GLOBALS['jsonFile']);
    $list = json_decode($list, true);

    $id--;

    // Update row with new name
    $list[$id]["category"] = $category;

    // Encode as JSON
    $list = json_encode($list, JSON_PRETTY_PRINT);

    // Update JSON file, return true, and refresh
    if (file_put_contents($GLOBALS['jsonFile'], $list)) {
        return true;
        header('location: index.php');
    }
}

/* Read categories */
function getCat() {
    $cat = file_get_contents($GLOBALS['jsonFile']);
    $cat = json_decode($cat, true);

    $cat = array_column($cat, 'category');
    $cat = array_unique($cat);

    return $cat;
}

/* Update category */
function updateCat(int $id, $newCategory) {
    $list = file_get_contents($GLOBALS['jsonFile']);
    $list = json_decode($list, true);

    $id--;

    // Update row with new name
    $list[$id]["category"] = $newCategory;

    // Encode as JSON
    $list = json_encode($list, JSON_PRETTY_PRINT);

    // Update JSON file, return true, and refresh
    if (file_put_contents($GLOBALS['jsonFile'], $list)) {
        return true;
        header('location: index.php');
    }    
}

/* Delete a category or set to blank */
function deleteCat(int $id) {
    $list = file_get_contents($GLOBALS['jsonFile']);
    $list = json_decode($list, true);

    $id--;

    // Update row with new name
    $list[$id]["category"] = "";

    // Encode as JSON
    $list = json_encode($list, JSON_PRETTY_PRINT);

    // Update JSON file, return true, and refresh
    if (file_put_contents($GLOBALS['jsonFile'], $list)) {
        return true;
        header('location: index.php');
    }    
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
    </style>
  </head>

  <body>
      
    <header class="hero is-link is-small">
        <div class="hero-body">
            <div class="container has-text-centered">
                <h1 class="title is-3">Your Grocery List</h1>
                <h2 class="subtitle is-5">A simple way to shop.</h2>
                <nav>
                    <a href="index.php" class="button is-small is-warning">HOME</a>
                    <a href="#" id="search" class="button is-small is-warning">SEARCH</a>
                    <a href="#" onclick="toggle_visibility('updateCatModal')" class="button is-small is-warning toggle">CATEGORIES</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Modal -->
    <div class="modal" id="updateCatModal">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">Update Categories</p>
                <button class="delete" aria-label="close" onclick="toggle_visibility('updateCatModal')"></button>
            </header>
            <section class="modal-card-body">

                <nav class="level">
                    <div class="level-left">
                        <ul class="level-item">
                            <?php
                                $categories = getCat();
                                foreach($categories as $category) { 
                                    if ($category == "Select Category") {
                                        echo ''; 
                                    }  
                                    echo '<li>' . $category . '</li>';
                                }
                            ?>
                        </ol>                    
                    </div>
                    <form class="container level-right" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                        <div class="field has-addons level-item has-text-centered">
                            <p class="control">
                                <input type="text" class="input" placeholder="Add an item" name="addItem" autofocus required>
                            </p>
                            <p class="control">
                                <input type="hidden" name="addId" value="<?php echo currentIndex($GLOBALS['index']); ?>" required>
                                <button class="button is-success" type="submit">Add</button>
                            </p>
                        </div>
                    </form>
                </nav>
            
            </section>
            <footer class="modal-card-foot">
                <button class="button is-success">Save changes</button>
                <button class="button" onclick="toggle_visibility('updateCatModal')">Cancel</button>
            </footer>
        </div>
    </div>

    <?php 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Create
    if(isset($_POST["addItem"]) && isset($_POST["addId"]) && isset($_POST["addCat"])) {
            $addItem = test_input($_POST["addItem"]);
            $addId = test_input($_POST["addId"]) + 1;
            $addCat = test_input($_POST["addCat"]);
    
            if(addItem($addId, $addItem, $addCat)) {
                echo "<div class=\"notification is-success is-light has-text-centered\" style=\"padding: 10px 20px;\"><button class=\"delete\"></button>Added <strong>$addItem</strong> under <strong>$addCat</strong>.</div>";
            }
    }

    // Update
    elseif(isset($_POST["updateItem"]) && isset($_POST["updateId"]) && isset($_POST["updateCat"])) {
            $updateItem = test_input($_POST["updateItem"]);
            $updateId = test_input($_POST["updateId"]);
            $updateCat = test_input($_POST["updateCat"]);
            
    
            if(updateItem($updateId, $updateItem, $updateCat)) {
                echo "<div class=\"notification is-success is-light has-text-centered\" style=\"padding: 10px 20px;\"><button class=\"delete\"></button>Updated to <strong>$updateItem</strong> under <strong>$updateCat</strong>.</div>";
            }
    }

    // Delete
    elseif(isset($_POST["deleteItem"]) && isset($_POST["deleteId"])) {
            $deleteItem = test_input($_POST["deleteItem"]);
            $deleteId = test_input($_POST["deleteId"]);
            
            if(deleteItem($deleteId)) {
                echo "<div class=\"notification is-success is-light has-text-centered\" style=\"padding: 10px 20px;\"><button class=\"delete\"></button><strong>$deleteItem</strong> deleted.</div>";
            }
    }
}

?>

    <nav class="level">
        <form class="container level-right" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <div class="field has-addons level-item has-text-centered">
                <p class="control">
                    <input type="text" class="input" placeholder="Add an item" name="addItem" autofocus required>
                    <div class="select">
                        <select name="addCat">
                            <option value="Select Category">Select Category</option>
                        <?php
                            $categories = getCat();
                            print_r($categories);
                            foreach($categories as $category) { 
                                echo '<option value=" ' . $category . '">' . $category . '</option>';
                            }
                        ?>
                        </select>
                    </div>
                </p>
                <p class="control">
                    <input type="hidden" name="addId" value="<?php echo currentIndex($GLOBALS['index']); ?>" required>
                    <button class="button is-success" type="submit">Add</button>
                </p>
            </div>
        </form>
    </nav>

    <section class="section columns">
        <div class="container column is-full-desktop has-text-centered">
            <h2 class="title is-5">Items Total: <?php echo currentIndex($GLOBALS['index']); ?></h2>
            
            <ul>
            <?php 
                // Variable for data; get from readItem function
                $data = readItem();

                // Check file if empty
                if(!empty(readItem())){

                    // Not empty? Read file data
                    foreach($data as $row) {

                        echo '<div class="buttons is-grouped is-centered">
                        <form method="POST" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">
                            <input type="text" class="button is-small is-outlined has-text-left" name="updateItem" value="' . $row->name . '">
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

                        <form method="POST" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">
                            <input type="hidden" name="deleteItem" value="' . $row->name . '">
                            <input type="hidden" name="deleteId" value="' .  $row->id . '">
                            <button class="button is-small is-danger is-outlined" type="submit">X</button>
                        </form>
                    </div>
                        ';
                    }
                }
                // Empty? Echo this
                else {
                    echo "No items.";
                }
            ?>
            </ul>

        </div>
    </section>

    <footer class="footer">
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

        function toggle_visibility(id) {
            var e = document.getElementById(id);
            if(e.style.display == 'block')
                e.style.display = 'none';
            else
                e.style.display = 'block';
        }
    </script>
</body>
</html>