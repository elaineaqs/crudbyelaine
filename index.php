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
function addItem(int $id, $itemName) {
    $list = file_get_contents($GLOBALS['jsonFile']);
    $list = json_decode($list, true);

    // Add 1
    $add_item = array(
            'id' => $id,
            'name' => $itemName,
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
function updateItem(int $id, $newName) {
    $list = file_get_contents($GLOBALS['jsonFile']);
    $list = json_decode($list, true);

    $id--;

    // Update row with new name
    $list[$id]["name"] = $newName;

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
      
    <header class="hero is-link is-medium">
        <div class="hero-body">
            <div class="container has-text-centered">
                <h1 class="title is-3">Your Grocery List</h1>
                <h2 class="subtitle is-5">A simple way to shop.</h2>
                <a href="index.php" class="button is-small is-warning">HOME</a>
            </div>
        </div>
    </header>

    <?php 

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    // Create
    if(isset($_GET["addItem"]) && isset($_GET["addId"])) {
            $addItem = test_input($_GET["addItem"]);
            $addId = test_input($_GET["addId"]) + 1;
    
            if(addItem($addId, $addItem)) {
                echo "<div class=\"notification is-success is-light has-text-centered\" style=\"padding: 10px 20px;\">Added <strong>$addItem</strong>.</div>";
            }
    }

    // Update
    elseif(isset($_GET["updateItem"]) && isset($_GET["updateId"])) {
            $updateItem = test_input($_GET["updateItem"]);
            $updateId = test_input($_GET["updateId"]);
            
    
            if(updateItem($updateId, $updateItem)) {
                echo "<div class=\"notification is-success is-light has-text-centered\" style=\"padding: 10px 20px;\">Updated to <strong>$updateItem</strong>.</div>";
            }
    }

    // Delete
    elseif(isset($_GET["deleteItem"]) && isset($_GET["deleteId"])) {
            $deleteItem = test_input($_GET["deleteItem"]);
            $deleteId = test_input($_GET["deleteId"]);
            
            if(deleteItem($deleteId)) {
                echo "<div class=\"notification is-success is-light has-text-centered\" style=\"padding: 10px 20px;\"><strong>$deleteItem</strong> deleted.</div>";
            }
    }
}

?>

    <nav class="level">
        <form class="container level-right" method="GET" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
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
                        <form method="GET" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">
                            <input type="text" class="button is-outlined" name="updateItem" value="' . $row->name . '">
                            <input type="hidden" name="updateId" value="' .  $row->id . '">
                            <button class="button is-info" type="submit">Save changes</button>
                        </form>

                        <form method="GET" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">
                            <input type="hidden" name="deleteItem" value="' . $row->name . '">
                            <input type="hidden" name="deleteId" value="' .  $row->id . '">
                            <button class="button is-danger is-outlined" type="submit">X</button>
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

</body>
</html>