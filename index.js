const menuButton = document.querySelector("#menu");
const nav = document.querySelector("nav");
const section = document.querySelector("section");
const busca = document.querySelector("input[name='busca']");
const clearButton = document.querySelector("#limparBuscar");
const tableContainer = document.querySelector("#table-container");

const buttonDelete = document.querySelector("#delete");
const dialogContainerDelete = document.querySelector("#dialogContainerDelete");
const buttonCancelDelete = document.querySelector("#cancelDelete");
const dialogConfirmDelete = document.querySelector("#confirmDelete");

if (menuButton) {
  menuButton.addEventListener("click", function () {
    nav.classList.toggle("hidden");
    section.classList.toggle("rounded-tl-lg");
    tableContainer.classList.toggle("max-w-[1000px]");
  });
}

document.addEventListener("DOMContentLoaded", function () {
  const searchForm = document.getElementById("searchForm");

  // Restaurar estado do campo de busca e botão de limpar
  const savedBusca = sessionStorage.getItem("buscaValue");
  const savedClearButtonState = sessionStorage.getItem("clearButtonHidden");

  if (savedBusca !== null) {
    busca.value = savedBusca;
    clearButton.classList.toggle("hidden", savedClearButtonState === "true");
  }

  // Manipulador de eventos para o campo de busca
  if (busca) {
    busca.addEventListener("input", function () {
      clearButton.classList.toggle("hidden", this.value === "");
    });
  }

  // Manipulador de eventos para o botão de limpar
  if (clearButton) {
    clearButton.addEventListener("click", () => {
      busca.value = "";
      clearButton.classList.add("hidden");
      searchForm.submit();
    });
  }

  // Salvar estado antes de submeter o formulário
  searchForm.addEventListener("submit", function () {
    sessionStorage.setItem("buscaValue", busca.value);
    sessionStorage.setItem(
      "clearButtonHidden",
      clearButton.classList.contains("hidden")
    );
  });
});

const snackbar = document.querySelector("#snackbar");

if (snackbar) {
  setTimeout(function () {
    snackbar.classList.add("opacity-0", "-translate-y-full");
    snackbar.classList.remove("opacity-100", "translate-y-0");
  }, 3000);
}
const buttonLogout = document.querySelector("#logout");
const dialogConfirmLogout = document.querySelector("#confirmLogout");
const dialogContainer = document.querySelector("#dialogContainer");
const buttonCancel = document.querySelector("#cancel");

buttonLogout.addEventListener("click", function () {
  dialogConfirmLogout.open = true;
  dialogContainer.classList.remove("hidden");
});

if (buttonCancel) {
  buttonCancel.addEventListener("click", function () {
    dialogConfirmLogout.close();
    dialogContainer.classList.add("hidden");
  });
}

if (buttonCancelDelete) {
  buttonCancelDelete.addEventListener("click", function () {
    dialogConfirmDelete.close();
    dialogContainerDelete.classList.add("hidden");
  });
}

function openConfirmDelete(id) {
  document.getElementById("id").value = id;
  dialogContainerDelete.classList.remove("hidden");
  dialogConfirmDelete.showModal();
}
