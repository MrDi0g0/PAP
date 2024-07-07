function validar()
{
    var email = document.getElementById("email").value;

    if(email == email)
    {
        alert("Essa conta ja existe!!!")
    }
    else
    {
        header('Location: Login.php');
    }
}