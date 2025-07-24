<!-- Meta tags for hamburger panel -->
<meta name="user-name" content="<?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User' ?>">
<meta name="page-type" content="user">

<!-- User Hamburger Panel CSS and JS -->
<link rel="stylesheet" href="../assets/css/hamburger-user.css">
<script src="../assets/hotel-ease.js"></script>

<!-- Set user name for JavaScript -->
<script>
    window.userName = '<?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User' ?>';
</script>
