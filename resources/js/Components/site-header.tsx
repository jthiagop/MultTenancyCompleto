import { NavUser } from "@/Components/nav-user"
import { NavActions } from "@/Components/nav-actions"
import { usePage } from "@inertiajs/react"
import { useState, useEffect } from "react"

export function SiteHeader() {
  const { auth } = usePage().props as any;
  const user = {
    name: auth?.user?.name || "Guest",
    email: auth?.user?.email || "guest@example.com",
    avatar: auth?.user?.avatar || "/assets/media/avatars/blank.png",
  }

  const [isScrolled, setIsScrolled] = useState(false);

  useEffect(() => {
    const handleScroll = () => {
      setIsScrolled(window.scrollY > 0);
    };

    window.addEventListener("scroll", handleScroll);
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  return (
    <header className={`flex h-[--header-height] shrink-0 items-center gap-2 transition-[width,height] ease-linear group-has-[[data-collapsible=icon]]/sidebar-wrapper:h-[--header-height] sticky top-0 z-10 bg-background ${isScrolled ? 'border-b shadow-sm' : ''}`}>
      <div className="flex w-full items-center gap-1 px-4 lg:gap-2 lg:px-6">
        <h1 className="text-base font-medium">Documents</h1>
        <div className="ml-auto flex items-center gap-2">
          <NavActions />
          <NavUser user={user} />
        </div>
      </div>
    </header>
  )
}
