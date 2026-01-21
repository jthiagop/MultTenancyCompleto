"use client"

import {
  ChevronRight,
  Globe,
  Home,
  LogOut,
  MessageSquare,
  Moon,
  Settings,
  User,
  Users,
} from "lucide-react"

import {
  Avatar,
  AvatarFallback,
  AvatarImage,
} from "@/Components/ui/avatar"
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@/Components/ui/dropdown-menu"
import {
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  useSidebar,
} from "@/Components/ui/sidebar"
import { Button } from "@/Components/ui/button"
import { Switch } from "@/Components/ui/switch"
import { useTheme } from "@/Components/theme-provider"

export function NavUser({
  user,
}: {
  user: {
    name: string
    email: string
    avatar: string
  }
}) {
  const { isMobile } = useSidebar()
  const { setTheme, theme } = useTheme()

  return (
    <SidebarMenu>
      <SidebarMenuItem>
        <DropdownMenu>
          <DropdownMenuTrigger asChild>
            <SidebarMenuButton
              size="lg"
              className="w-auto p-0 hover:bg-transparent data-[state=open]:bg-transparent"
            >
              <Avatar className="h-8 w-8 rounded-full border-2 border-emerald-500">
                <AvatarImage src={user.avatar} alt={user.name} />
                <AvatarFallback className="rounded-full">CN</AvatarFallback>
              </Avatar>
            </SidebarMenuButton>
          </DropdownMenuTrigger>
          <DropdownMenuContent
            className="w-64 p-0"
            side={isMobile ? "bottom" : "top"}
            align="end"
            sideOffset={4}
          >
            <div className="flex items-center gap-3 px-6 py-4">
               <div className="relative">
                  <Avatar className="h-8 w-8 rounded-full border-2 border-emerald-500">
                    <AvatarImage src={user.avatar} alt={user.name} />
                    <AvatarFallback className="rounded-full">CN</AvatarFallback>
                  </Avatar>
               </div>
               <div className="flex flex-col">
                  <span className="font-semibold text-foreground text-sm">{user.name}</span>
                  <span className="text-muted-foreground text-xs">{user.email}</span>
               </div>
               <span className="ml-auto rounded bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-600">Pro</span>
            </div>
            
            <DropdownMenuSeparator className="m-0" />
            
            <DropdownMenuGroup className="p-2">
              <DropdownMenuItem className="gap-3 py-2.5">
                <Users className="h-4 w-4 text-muted-foreground" />
                Public Profile
              </DropdownMenuItem>
              <DropdownMenuItem className="gap-3 py-2.5">
                <User className="h-4 w-4 text-muted-foreground" />
                My Profile
              </DropdownMenuItem>
              <DropdownMenuItem className="gap-3 py-2.5">
                <Settings className="h-4 w-4 text-muted-foreground" />
                My Account
                <ChevronRight className="ml-auto h-4 w-4 text-muted-foreground" />
              </DropdownMenuItem>
              <DropdownMenuItem className="gap-3 py-2.5">
                <MessageSquare className="h-4 w-4 text-muted-foreground" />
                Dev Forum
              </DropdownMenuItem>
            </DropdownMenuGroup>
            
            <DropdownMenuSeparator className="m-0" />

            <div className="p-4 flex flex-col gap-4">
              <div className="flex items-center justify-between px-2">
                 <div className="flex items-center gap-3">
                    <Moon className="h-4 w-4 text-muted-foreground" />
                    <span className="text-sm">Modo Noite</span>
                 </div>
                 <Switch
                    checked={theme === "dark"}
                    onCheckedChange={(checked) => setTheme(checked ? "dark" : "light")}
                 />
              </div>
              
              <Button variant="outline" className="w-full justify-center gap-2">
                 <LogOut className="h-4 w-4 text-muted-foreground" />
                 Sair
              </Button>
            </div>
          </DropdownMenuContent>
        </DropdownMenu>
      </SidebarMenuItem>
    </SidebarMenu>
  )
}
