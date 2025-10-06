<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
<title>Calendly Clone</title>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #333;
    min-height: 100vh;
    position: relative;
    overflow-x: hidden;
}

/* Animated Background Pattern */
body::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: 
        radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
    pointer-events: none;
    animation: float 20s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

header {
    background: rgba(255, 255, 255, 0.98);
    padding: 25px 20px;
    text-align: center;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
    backdrop-filter: blur(10px);
    position: relative;
    z-index: 10;
}

header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2);
    border-radius: 2px;
}

h1 {
    margin: 0;
    font-size: 2.8rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 700;
    letter-spacing: -1px;
    animation: slideDown 0.6s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.container {
    max-width: 650px;
    margin: 60px auto;
    background: rgba(255, 255, 255, 0.98);
    padding: 50px 40px;
    border-radius: 24px;
    box-shadow: 
        0 20px 60px rgba(0, 0, 0, 0.15),
        0 0 0 1px rgba(255, 255, 255, 0.5);
    text-align: center;
    position: relative;
    z-index: 5;
    animation: fadeInUp 0.8s ease-out;
    backdrop-filter: blur(10px);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(40px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.container::before {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(135deg, #667eea, #764ba2, #f093fb, #667eea);
    border-radius: 24px;
    z-index: -1;
    opacity: 0;
    transition: opacity 0.3s ease;
    background-size: 200% 200%;
    animation: gradientShift 4s ease infinite;
}

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.container:hover::before {
    opacity: 0.6;
}

.container h2 {
    font-size: 2.2rem;
    color: #2d3748;
    margin-bottom: 16px;
    font-weight: 700;
    line-height: 1.2;
}

.container p {
    font-size: 1.15rem;
    color: #718096;
    margin-bottom: 40px;
    line-height: 1.6;
}

button {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    padding: 16px 40px;
    border: none;
    border-radius: 12px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    margin: 10px 8px;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    position: relative;
    overflow: hidden;
    min-width: 140px;
}

button::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

button:hover::before {
    width: 300px;
    height: 300px;
}

button:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
}

button:active {
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

a {
    text-decoration: none;
    color: #667eea;
    margin: 10px;
    font-weight: 600;
    position: relative;
    transition: color 0.3s ease;
    display: inline-block;
}

a::after {
    content: '';
    position: absolute;
    bottom: -4px;
    left: 0;
    width: 0;
    height: 2px;
    background: linear-gradient(90deg, #667eea, #764ba2);
    transition: width 0.3s ease;
}

a:hover {
    color: #764ba2;
}

a:hover::after {
    width: 100%;
}

/* Responsive Design */
@media (max-width: 768px) {
    h1 {
        font-size: 2rem;
    }
    
    .container {
        margin: 30px 20px;
        padding: 40px 25px;
    }
    
    .container h2 {
        font-size: 1.8rem;
    }
    
    .container p {
        font-size: 1rem;
    }
    
    button {
        width: 100%;
        margin: 8px 0;
    }
}

@media (max-width: 480px) {
    h1 {
        font-size: 1.6rem;
    }
    
    .container {
        padding: 30px 20px;
    }
    
    .container h2 {
        font-size: 1.5rem;
    }
}
</style>
<script>
function goTo(page){
    window.location.href = page;
}
</script>
</head>
<body>
<header>
    <h1>Calendly Clone</h1>
</header>
<div class="container">
    <h2>Schedule Meetings Effortlessly</h2>
    <p>Create your own booking link and let others book time with you!</p>
    <button onclick="goTo('signup.php')">Sign Up</button>
    <button onclick="goTo('login.php')">Log In</button>
</div>
</body>
</html>
