/**
 * Sidebar Menu Active State Handler
 * Automatically detects and expands menu items based on current URL
 */

"use strict";

// Initialize menu active state on DOM ready
document.addEventListener("DOMContentLoaded", function() {
    initSidebarMenuActiveState();
});

/**
 * Initialize sidebar menu active state detection
 */
function initSidebarMenuActiveState() {
    const menu = document.querySelector("#kt_app_sidebar_menu");
    
    if (!menu) {
        return;
    }

    const currentUrl = window.location.href;
    const menuLinks = menu.querySelectorAll(".menu-link");

    menuLinks.forEach(function(link) {
        const linkElement = link.tagName === 'A' ? link : link.closest('a');
        
        if (!linkElement) {
            return;
        }

        const linkUrl = linkElement.getAttribute("href");
        
        // Check if current URL matches the link
        if (linkUrl && currentUrl.includes(linkUrl) && linkUrl !== '#') {
            // Add active class to the link
            linkElement.classList.add("active");
            
            // Find and expand all parent accordion menus
            let parent = linkElement.closest(".menu-item");
            
            while (parent) {
                // If parent is an accordion menu item
                if (parent.classList.contains("menu-accordion")) {
                    parent.classList.add("here", "show");
                    
                    // Find and show the submenu
                    const submenu = parent.querySelector(".menu-sub-accordion");
                    if (submenu) {
                        submenu.classList.add("show");
                        submenu.style.display = "block";
                    }
                }
                
                // Move up to next parent menu item
                parent = parent.parentElement?.closest(".menu-item");
            }
        }
    });
}

/**
 * Alternative method using Metronic's KTMenu component
 * This will be called after KTMenu is initialized
 */
if (typeof KTMenu !== 'undefined') {
    KTMenu.createInstances();
    
    // Wait for menu to be fully initialized
    setTimeout(function() {
        const menuElement = document.querySelector("#kt_app_sidebar_menu");
        if (menuElement) {
            const menuInstance = KTMenu.getInstance(menuElement);
            if (menuInstance) {
                // The menu will automatically handle active states
                // based on the current URL matching href attributes
                menuInstance.update();
            }
        }
    }, 100);
}
