document.addEventListener("DOMContentLoaded", function() {
    const deleteButtons = document.querySelectorAll(".btn-danger");
    deleteButtons.forEach(button => {
        button.addEventListener("click", function(event) {
            if (!confirm("¿Estás seguro de eliminar este registro?")) {
                event.preventDefault();
            }
        });
    });
});
