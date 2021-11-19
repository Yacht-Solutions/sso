<div class="container well shadow-lg p-5" id="logout-form">
    <h3 class="mb-3">Hello <?=htmlspecialchars($_SESSION['user']->login)?>&nbsp;!</h3>
    <button type="submit" class="btn btn-primary">Sign out</button>
</div>
<script>

    document.querySelector('#logout-form button').addEventListener('click', e => {

        e.preventDefault()
        window.location = 'http://localhost:8300/?action=logout&to=http://<?=$_SERVER['HTTP_HOST']?>'
    })

</script>
