let signuponly = document.querySelector(".signuponly")
let signupBtn = document.getElementById("signupBtn")
let signupTxt = document.getElementById("signupTxt")
let signTxt = document.getElementById("signTxt")
let loginbtn = document.getElementById("loginbtn")
let gobackTxt = document.getElementById("goBackTxt")
let gotologin = document.getElementById("gotologin")

let selectCountry = document.querySelector('.selectCountry')
let arrow = document.querySelector('.arrow')
let countryMenu = document.querySelector('.countryMenu')
let options = document.querySelectorAll('.countryMenu li')
let flag = document.querySelector('.flag')
let listItems = document.querySelectorAll('.countryMenu li')

let countryInput = document.getElementById("countryInput")
let formtype = document.getElementById("formtype")

signupBtn.addEventListener("click", function () {
    signuponly.classList.toggle("active")

    //Regisztrációs részleg
    if (signuponly.classList.contains("active")) {
        formtype.value = "signup"
        signuponly.hidden = false
        signupTxt.hidden = true
        gobackTxt.hidden = false
        
        signTxt.innerText = "Regisztráció"
        loginbtn.innerText = "Regisztráció"

        currentForm = "signup"
    } else {
        signuponly.hidden = true
        formtype.value = "login"
        signupTxt.hidden = false
        gobackTxt.hidden = true
        signTxt.innerText = "Bejelentkezés"
        loginbtn.innerText = "Bejelentkezés"
    }
})

gotologin.addEventListener("click", function () {
    signuponly.classList.remove("active")
    signuponly.hidden = true
    formtype.value = "login"
    
    signupTxt.hidden = false
    gobackTxt.hidden = true
    
    signTxt.innerText = "Bejelentkezés"
    loginbtn.innerText = "Bejelentkezés"
})


//Minden ország zászlaja
listItems.forEach(item => {
    const dataValue = item.getAttribute('data-value')
    const img = item.querySelector('img')
    if (dataValue && img) {
        img.classList.add("menuFlag" + dataValue, "menuFlag")
    }

    item.querySelector('img').src = `https://purecatamphetamine.github.io/country-flag-icons/3x2/${dataValue}.svg`
    const countryName = new Intl.DisplayNames(['hu'], { type: 'region' }).of(dataValue)
    const textNode = [...item.childNodes].find(node => node.nodeType === Node.TEXT_NODE)
    if (textNode && countryName) {
        textNode.textContent = countryName
    }
})


selectCountry.addEventListener('click', () => {

    selectCountry.classList.toggle('selectedCountry-clicked')
    arrow.classList.toggle('arrow-rotate')
    countryMenu.classList.toggle('countryMenu-open')
})

options.forEach(option => {
    option.addEventListener('click', () => {

        flag.src = `https://purecatamphetamine.github.io/country-flag-icons/3x2/${option.dataset.value}.svg`
        selectCountry.classList.remove('selectedCountry-clicked')
        arrow.classList.remove('arrow-rotate')
        countryMenu.classList.remove('countryMenu-open')
        countryInput.value = option.dataset.value
        options.forEach(opt => {
            opt.classList.remove('activeCountry')
        })
        option.classList.add('activeCountry')
    })
})
