fetch("header.html")
    .then((res) => res.text())
    .then((data) => (document.getElementById("header").innerHTML = data));

//password match validation
document.querySelector('#signup').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm-password').value;
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Passwords do not match!');
        return;
    }
    
    if (password.length < 8) {
        e.preventDefault();
        alert('Password must be at least 8 characters long');
        return;
    }
    
    //if everything is valid
    alert('Account created successfully! (Demo)');
});

//real-time password length validation
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const hint = document.querySelector('.password-hint');
    
    if (password.length > 0 && password.length < 8) {
        hint.style.color = '#e63946';
    } else if (password.length >= 8) {
        hint.style.color = '#2a9d8f';
    }
});