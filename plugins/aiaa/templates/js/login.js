window.onload = function() {

    if (window.location.href.indexOf("wp-login.php") > -1) {

        // Get all elements in the document
        var elements = document.body.getElementsByTagName('*');

        // Iterate over all elements
        for (var i = 0; i < elements.length; i++) {
            // Get the innerHTML of the current element
            var html = elements[i].innerHTML;

            // Replace all occurrences of " | " with an empty string
            var newHtml = html.replace(/ \| /g, '');

            // Set the new HTML of the current element
            elements[i].innerHTML = newHtml;

        }

        const h1 = document.querySelector("#login h1");

        // Create an image element
        const img = document.createElement('img');
        img.src = '/wp-content/plugins/aiaa/templates/images/AIAA 01-01.png';
        img.alt = 'Alexandria Independent Auto Appraisers';
        img.width = 300;

        // Append the image to the h1
        h1.appendChild(img);

        const p = document.createElement('p');
        p.textContent = 'Please Login to your account to start an appraisal.';
        // add font-size 12px to the p
        p.style.fontSize = '18px';

        h1.appendChild(p);

        const nav = document.querySelector("#login #nav");
        const dontHaveAcct = document.createTextNode("Don't have an account? ");
        // Create the anchor element
        const signup = document.createElement('a');
        signup.href = '/register';
        signup.textContent = 'Sign up!';

        // Prepend a <br/> after the signup
        const br = document.createElement('br');
        nav.prepend(br);

        nav.prepend(signup);
        nav.prepend(dontHaveAcct);

    }

}