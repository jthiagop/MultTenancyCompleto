"use client"

import { useTheme } from "@/Components/theme-provider"
import { useSidebar } from "@/Components/ui/sidebar"

export function AppLogo() {
  const { theme } = useTheme()
  const { state } = useSidebar()
  
  const isCollapsed = state === "collapsed"
  const isDark = theme === "dark"
  
  return (
    <a href="/" className="flex items-center gap-2 px-2">
      {/* Light mode logos */}
      {!isDark && (
        <>
          <img 
            src="/assets/media/app/default-logo.svg" 
            alt="Dominus" 
            className={`min-h-[20px] max-w-none transition-opacity ${isCollapsed ? 'hidden' : 'block'}`}
          />
          <img 
            src="/assets/media/app/mini-logo.svg" 
            alt="Dominus" 
            className={`min-h-[20px] max-w-none transition-opacity ${isCollapsed ? 'block' : 'hidden'}`}
          />
        </>
      )}
      
      {/* Dark mode logos */}
      {isDark && (
        <>
          <img 
            src="/assets/media/app/default-logo-dark.svg" 
            alt="Dominus" 
            className={`min-h-[20px] max-w-none transition-opacity ${isCollapsed ? 'hidden' : 'block'}`}
          />
          <img 
            src="/assets/media/app/mini-logo.svg" 
            alt="Dominus" 
            className={`min-h-[25px] max-w-none transition-opacity ${isCollapsed ? 'block' : 'hidden'}`}
          />
        </>
      )}
    </a>
  )
}
