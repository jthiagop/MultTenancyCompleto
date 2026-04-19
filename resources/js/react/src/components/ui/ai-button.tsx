import { Sparkles } from 'lucide-react';
import { Link } from 'react-router-dom';
import './ai-button.css';

export function AIButton({ to, children }: { to: string; children: React.ReactNode }) {
  return (
    <div className="ai-btn-border">
      <Link
        to={to}
        className="flex items-center gap-2 h-9 px-4 text-sm font-medium rounded-[calc(0.5rem-1px)] bg-background text-foreground"
      >
        <Sparkles className="size-3.5 text-purple-500" />
        {children}
      </Link>
    </div>
  );
}
