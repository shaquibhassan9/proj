// Menu grouped by categories with relevant food images
const menuCategories = {
  "Chinese": {
    img: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTrSodh2_bz3T_LLA16yP9WdpyAoAUkC5-TdA&s",
    items: [
      { name: "Veg Chowmein", price: 50 },
      { name: "Egg Chowmein", price: 60 },
      { name: "Chilli Potato", price: 60 },
      { name: "Chilli Paneer", price: 100 }
    ]
  },
  "Snacks": {
    img: "https://images.themodernproper.com/production/posts/2022/Homemade-French-Fries_8.jpg?w=1200&h=1200&q=60&fm=jpg&fit=crop&dm=1662474181&s=15046582e76b761a200998df2dcad0fd",
    items: [
      { name: "Smiles", price: 50 },
      { name: "French Fry", price: 50 },
      { name: "Veg Nuggets", price: 50 }
    ]
  },
  "Burger": {
    img: "https://www.foodandwine.com/thmb/DI29Houjc_ccAtFKly0BbVsusHc=/1500x0/filters:no_upscale():max_bytes(150000):strip_icc()/crispy-comte-cheesburgers-FT-RECIPE0921-6166c6552b7148e8a8561f7765ddf20b.jpg",
    items: [
      { name: "Veg Burger", price: 40 },
      { name: "Cheese Burger", price: 50 },
      { name: "Paneer Cheese Tikki Burger", price: 65 }
    ]
  },
  "Pizza": {
    img: "https://media.istockphoto.com/id/1442417585/photo/person-getting-a-piece-of-cheesy-pepperoni-pizza.jpg?s=612x612&w=0&k=20&c=k60TjxKIOIxJpd4F4yLMVjsniB4W1BpEV4Mi_nb4uJU=",
    items: [
      { name: "Cheese Pizza", price: 130 },
      { name: "Onion Pizza", price: 130 },
      { name: "Mix Pizza", price: 150 }
    ]
  },
  "South Indian Dosa": {
    img: "https://www.awesomecuisine.com/wp-content/uploads/2009/06/Plain-Dosa.jpg",
    items: [
      { name: "Masala Dosa", price: 70 },
      { name: "Paneer Masala Dosa", price: 100 },
      { name: "Mysore Butter Masala Dosa", price: 95 }
    ]
  }
};

// Load cart from localStorage
let cart = JSON.parse(localStorage.getItem("cart")) || {};

function addToCart(name, price) {
  if (!cart[name]) cart[name] = { qty: 0, price };

  if (cart[name].qty < 10) {
    cart[name].qty++;
  } else {
    alert("Maximum 10 items allowed for " + name);
    return;
  }

  localStorage.setItem("cart", JSON.stringify(cart));
  renderCart();
}

function removeFromCart(name) {
  if (cart[name]) {
    cart[name].qty--;
    if (cart[name].qty <= 0) delete cart[name];
  }

  localStorage.setItem("cart", JSON.stringify(cart));
  renderCart();
}

function renderMenu() {
  const menuDiv = document.getElementById("menu");
  menuDiv.innerHTML = "";

  Object.keys(menuCategories).forEach(category => {
    const cat = menuCategories[category];
    const categoryDiv = document.createElement("div");
    categoryDiv.className = "category";

    categoryDiv.innerHTML = `
      <div class="category-header">
        <img src="${cat.img}" alt="${category}">
        <h3>${category}</h3>
        <span>▶</span>
      </div>
      <div class="items"></div>
    `;

    const itemsDiv = categoryDiv.querySelector(".items");

    cat.items.forEach(item => {
      const itemDiv = document.createElement("div");
      itemDiv.className = "item";
      itemDiv.innerHTML = `
        <span>${item.name} - Rs ${item.price}</span>
        <button onclick="addToCart('${item.name}', ${item.price})">Add</button>
      `;
      itemsDiv.appendChild(itemDiv);
    });

    categoryDiv.querySelector(".category-header").addEventListener("click", function () {
      const open = itemsDiv.style.display === "block";
      document.querySelectorAll(".items").forEach(i => i.style.display = "none");
      document.querySelectorAll(".category-header").forEach(h => h.classList.remove("active"));
      if (!open) {
        itemsDiv.style.display = "block";
        this.classList.add("active");
      }
    });

    menuDiv.appendChild(categoryDiv);
  });
}

function renderCart() {
  const cartItems = document.getElementById("cartItems");
  cartItems.innerHTML = "";
  let total = 0;

  Object.keys(cart).forEach(name => {
    const item = cart[name];
    const li = document.createElement("li");

    li.innerHTML = `
      ${name} x ${item.qty} = Rs ${item.qty * item.price}
      <button class="remove" onclick="removeFromCart('${name}')">Remove</button>
    `;

    cartItems.appendChild(li);
    total += item.qty * item.price;
  });

  document.getElementById("total").innerText = total;
}

// FINAL CHECKOUT FUNCTION (includes saving order + WhatsApp)
function checkout() {
  if (Object.keys(cart).length === 0) {
    alert("Your cart is empty!");
    return;
  }

  let itemsText = "";
  let total = 0;

  Object.keys(cart).forEach(name => {
    const c = cart[name];
    itemsText += `${name} x ${c.qty}, `;
    total += c.qty * c.price;
  });

  itemsText = itemsText.slice(0, -2);

  const customer = prompt("Enter your name:");
  if (!customer) {
    alert("Name is required!");
    return;
  }

  // Save order to database
  fetch("save_order.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      items: itemsText,
      total: total,
      customer: customer
    })
  })
    .then(() => {
      const msg =
        `Order Summary:\n${itemsText}\nTotal: Rs ${total}\nCustomer: ${customer}`;
            
        const whatsappURL = "https://api.whatsapp.com/send?phone=919999999999&text=" + encodeURIComponent(msg);

      localStorage.removeItem("cart");
      window.location.href = whatsappURL;
    });
}

renderMenu();
renderCart();
