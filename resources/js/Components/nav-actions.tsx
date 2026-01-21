import { Search, BellDot, MessageSquareMore, LayoutGrid } from "lucide-react"
import { Button } from "@/Components/ui/button"

export function NavActions() {
  return (
    <div className="flex items-center">
      <Button
        variant="ghost"
        size="icon"
        className="h-10 w-10"
        aria-label="Search"
      >
        <Search className="h-5 w-5 text-muted-foreground" />
      </Button>
      <Button
        variant="ghost"
        size="icon"
        className="h-10 w-10"
        aria-label="Notifications"
      >
        <BellDot className="h-5 w-5 text-muted-foreground" />
      </Button>
      <Button
        variant="ghost"
        size="icon"
        className="h-10 w-10"
        aria-label="Messages"
      >
        <MessageSquareMore className="h-5 w-5 text-muted-foreground" />
      </Button>
      <Button
        variant="ghost"
        size="icon"
        className="h-10 w-10"
        aria-label="Apps"
      >
        <LayoutGrid className="h-5 w-5 text-muted-foreground" />
      </Button>
    </div>
  )
}
