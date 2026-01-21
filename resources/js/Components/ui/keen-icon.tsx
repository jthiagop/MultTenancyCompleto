import React from "react"
import { cn } from "@/lib/utils"

type KeenIconStyle = "duotone" | "outline" | "solid"

interface KeenIconProps extends React.SVGProps<SVGSVGElement> {
  /**
   * Nome do ícone (ex: "home", "user", "settings")
   */
  name: string
  
  /**
   * Estilo do ícone
   * @default "outline"
   */
  variant?: KeenIconStyle
  
  /**
   * Tamanho do ícone em pixels
   * @default 24
   */
  size?: number
  
  /**
   * Classes CSS adicionais
   */
  className?: string
}

/**
 * Componente para usar ícones KeenIcons SVG
 * 
 * @example
 * <KeenIcon name="home" variant="outline" size={20} />
 * <KeenIcon name="user" variant="duotone" className="text-primary" />
 */
export function KeenIcon({ 
  name, 
  variant = "outline", 
  size = 24,
  className,
  ...props 
}: KeenIconProps) {
  const iconPath = `/assets/keenicons/${variant}/fonts/keenicons-${variant}.svg#${name}`
  
  return (
    <svg
      width={size}
      height={size}
      className={cn("keen-icon", className)}
      {...props}
    >
      <use xlinkHref={iconPath} />
    </svg>
  )
}

/**
 * Componente para usar imagens SVG diretas dos keenicons
 * Útil quando você tem o caminho completo do SVG
 */
export function KeenIconImage({ 
  src, 
  alt = "icon",
  className,
  ...props 
}: React.ImgHTMLAttributes<HTMLImageElement>) {
  return (
    <img 
      src={src}
      alt={alt}
      className={cn("keen-icon", className)}
      {...props}
    />
  )
}
