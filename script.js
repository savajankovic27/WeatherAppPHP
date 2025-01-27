// didn't add too much here more so for the fancy interactive buttons 

document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("unit").addEventListener("change", function() {
        document.querySelector("form").submit();
    });
});
