<?php
  include "connect.php";

  // If page number is not set, initialize it to 1.
  if (!isset($_COOKIE['page'])) {
    setcookie("page", 1, time() + 300);
  }

  // Define the query to fetch the results.
  $query = "SELECT * FROM products WHERE '1'";

  if (isset($_POST["price"])) {
    $query = $query . " AND price <= " . $_POST['price'];
  }

  if (isset($_POST["category"]) && $_POST['category'] != '') {
    $query = $query . " AND category = '" . $_POST['category'] . "'";
    setcookie('category', $_POST['category'], time() + 3600);
  }
  else $query = $query . " AND category = '" . $_COOKIE['category'] . "'";

  if (isset($_POST['onSale'])) {
    $query = $query . " AND onSale = '1'";
    setCookie('onSale', 1, time() + 3600);
  }
  else setcookie('onSale', 0, time() + 3600);

  // Limit the number of results to 12.
  $query = $query . " Limit 12";

  // Add offset to skip results of previous page.
  if (isset($_COOKIE['page']) && $_COOKIE["page"] > 0) {
    $query = $query . " OFFSET " . ($_COOKIE["page"] - 1) * 12;
  }

  // Fetch the result.
  $result = mysqli_query($conn, $query) or die(mysqli_error($conn));
?>
<html>
  <head>
    <title>All Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <div class="mainContainer">
      <div class="left">
        <form action="" method="POST">
          <div class="section">
            <h3>Price</h3>
            <input type="range" min="4.99" max="999.99" id="priceFilter" value=<?php echo $_POST['price'] ?> name="price">
            <p class="priceValue">Showing products up to Rs. <span id="priceMax"></span></p>
            <hr />
          </div>
          <div class="section">
            <h3>Category</h3>
            <?php
              if (isset($_COOKIE['category']) && $_COOKIE['category'] != "")
              {
            ?>
            <p>Current category: <?php echo $_COOKIE['category'] ?></p>
            <?php
              }
            ?>
            <select name="category" id="category">
              <option value=""></option>
              <option value="Electronics">Electronics</option>
              <option value="Clothing">Clothing</option>
              <option value="Books">Books</option>
              <option value="Sports">Sports</option>
              <option value="Home & Kitchen">Home & Kitchen</option>
              <option value="Toys">Toys</option>
              <option value="Fashion Accessories">Electronics</option>
              <option value="Office Supplies">Office Supplies</option>
              <option value="Home Decor">Home Decor</option>
              <option value="Beauty">Beauty</option>
              <option value="Games">Games</option>
              <option value="Electronics Accessories">Electronics Accessories</option>
              <option value="Tools">Tools</option>
              <option value="Gardening">Gardening</option>
              <option value="Art Supplies">Art Supplies</option>
              <option value="Travel">Travel</option>
            </select>
            <hr />
          </div>
          <div class="section">
            Status: <input type="checkbox" <?php if ($_COOKIE['onSale'] == 1) echo "checked" ?> name="onSale" id="onSale" /> On Sale <br />
            <hr />
            <input type="submit" name="submit" class="btn btn-md btn-success">
          </div>
        </form>
      </div>
      <div class="right">
        <!-- Display all rows of results. -->
        <div class="productsContainer">
          <?php
            while ($row = mysqli_fetch_array($result))
            {
          ?>
          <div class="card" style="width: 20rem;">
            <img src="<?php echo $row['imageLink'] ?>" class="card-img-top" alt="Product">
            <div class="card-body">
              <hr />
              <h5 class="card-title"><?php echo $row['name'] ?></h5>
              <hr />
              <p class="card-text">Price Rs. <?php echo $row['price'] ?>/-</p>
              <p class="card-text">Category: <?php echo $row['category'] ?></p>
              <p class="card-text">Status: <?php echo ($row['onSale'] == '1') ? 'On Sale' : 'Not On Sale' ?></p>
            </div>
          </div>
          <?php
            }
          ?>

          <!-- If number of products in the result is zero. -->
          <?php
            if (mysqli_num_rows($result) == 0)
            {
          ?>
          <center>
            <h1 class="noProducts">No products found</h1>
          </center>
          <?php
            }
          ?>
        </div>
      </div>
    </div>
    <hr />
      <nav class="pagination">
        <ul class="pagination">
          <li class="page-item <?php if ($_COOKIE["page"] <= 1) echo "disabled"; ?>" onClick="previous()" >
            <a class="page-link" href="#">Previous</a>
          </li>
          <li class="page-item <?php if (mysqli_num_rows($result) < 12) echo "disabled"; ?>" onClick="next()" >
            <a class="page-link" href="#">Next</a>
          </li>
        </ul>
      </nav>
  </body>
  <script>
    let slider = document.getElementById("priceFilter");
    let output = document.getElementById("priceMax");
    output.innerHTML = slider.value;
    slider.oninput = function() {
      output.innerHTML = this.value;
    }

    // Function to get the page number.
    function getPageNumber() {
      let cookies = document.cookie.split("; ");
      for (let i=0; i<cookies.length; i++) {
        let cookie = cookies[i].split("=");
        if (cookie[0] == "page") return cookie[1];
    }
  }

    // Function when next button is clicked.
    function next() {
      let pageNumber = getPageNumber();
      let newCookie = `page=${parseInt(pageNumber) + 1}`;
      document.cookie = newCookie;
      location.reload();
    }

    // Function when previous button is clicked.
    function previous() {
      let pageNumber = getPageNumber();
      let newPageNumber = parseInt(pageNumber) - 1;
      if (newPageNumber < 0) newPageNumber = 0;
      let newCookie = `page=${newPageNumber}`;
      document.cookie = newCookie;
      location.reload();
    }
  </script>
</html>
