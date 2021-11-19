<div class="container well shadow-lg p-5" id="login-form">
    <h3 class="mb-3">Connexion</h3>
    <form>
        <div class="mb-3">
            <label for="login" class="form-label">Login</label>
            <input type="input" class="form-control" id="login" value="zidane" placeholder="bob marley">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" value="foot" placeholder="azerty">
        </div>
        <button type="submit" class="btn btn-primary">Log in</button>
    </form>
</div>
<script>

    document.querySelector('#login-form button').addEventListener('click', e => {

        e.preventDefault()
        $.post(
            '/?action=login',
            {
                login: document.querySelector('#login').value,
                password: document.querySelector('#password').value,
            },
            data => {

                data = JSON.parse(data)

                if(data.success)
                {
                    window.location = 'http://localhost:8300/?action=register&jwt=' + data.jwt + '&to=http://<?=$_SERVER['HTTP_HOST']?>'
                }
                else
                {
                    alert('Nope');
                }
            }
        )
    })

</script>
