// get current year
(function () {
    var year = new Date().getFullYear();
    document.querySelector("#currentYear").innerHTML = year;
})();
//using formspree
$(document).ready(function () {
    $('form').on('submit', function (e) {
        e.preventDefault(); // Prevent the form from submitting the traditional way
        $("#submitButton").prop("disabled", true); // Disable the submit button

        $.ajax({
            type: 'POST',
            url: 'https://formspree.io/f/mzzzkdav', // Path to your PHP file
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response) {
                  alert("Thank you very much. Your message has been sent.");
                  $("form")[0].reset();
                  $("#submitButton").prop("disabled", false); // Re-enable the submit button
                  //window.location.href = 'contact.html';
            },
            error: function () {
                alert("There was a problem with your submission. Please try again.")
                //window.location.href = 'contact.html';
                $("form")[0].reset();
                $("#submitButton").prop("disabled", false); // Re-enable the submit button
            }
        });
    });
});
//uaing sendEmail.php
/*
$(document).ready(function () {
    $('form').on('submit', function (e) {
        e.preventDefault(); // Prevent the form from submitting the traditional way
        $("#submitButton").prop("disabled", true); // Disable the submit button

        $.ajax({
            type: 'POST',
            url: 'sendEmail.php', // Path to your PHP file
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    alert(response.message);
                    $("form")[0].reset();
                      //window.location.href = 'contact.html';
                } else {
                    alert(response.message);
                    $("form")[0].reset();
                    //window.location.href = 'contact.html';
                }

                $("#submitButton").prop("disabled", false); // Re-enable the submit button


            },
            error: function () {
                alert(response.message)
                //window.location.href = 'contact.html';
                $("form")[0].reset();
                $("#submitButton").prop("disabled", false); // Re-enable the submit button
            }
        });
    });
});

*/
document.getElementById('toggleButton').addEventListener('click', function(event) {
  event.preventDefault();

  var hiddenItems = document.querySelectorAll('.hidden');
  var isShowing = hiddenItems[0].style.display === 'block';

  hiddenItems.forEach(function(item) {
    item.style.display = isShowing ? 'none' : 'block';
  });

  this.textContent = isShowing ? 'Voir Plus' : 'Voir Moins';
});
