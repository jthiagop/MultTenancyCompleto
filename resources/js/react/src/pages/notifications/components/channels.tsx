import React, { ReactNode, useEffect, useState } from 'react';
import { CardNotification } from '@/partials/cards';
import { Clock2Icon, Mail, Monitor, Phone, Slack, SquarePen } from 'lucide-react';
import { FaWhatsapp } from 'react-icons/fa';
import { Link } from 'react-router';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import {
  Sheet,
  SheetContent,
  SheetHeader,
  SheetTitle,
  SheetFooter,
} from '@/components/ui/sheet';
import { Input, InputAddon, InputGroup } from '@/components/ui/input';
import { Field, FieldGroup } from '@/components/ui/field';

interface IChannelsItem {
  icon: React.ElementType;
  title: string;
  description: string;
  button?: boolean;
  actions: ReactNode;
}
type IChannelsItems = Array<IChannelsItem>;

const isNotificationSupported = () =>
  typeof window !== 'undefined' && 'Notification' in window;

const Channels = () => {
  const [desktopEnabled, setDesktopEnabled] = useState(
    isNotificationSupported() && Notification.permission === 'granted',
  );
  const [whatsappSheetOpen, setWhatsappSheetOpen] = useState(false);
  const [whatsappHora, setWhatsappHora] = useState('08:00');
  const [whatsappHoraSaving, setWhatsappHoraSaving] = useState(false);
  const [whatsappHoraError, setWhatsappHoraError] = useState<string | null>(null);

  // Carrega o horário configurado para a empresa ao montar o componente
  useEffect(() => {
    fetch('/integracoes/whatsapp/horario', {
      headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    })
      .then((r) => r.json())
      .then((data) => {
        if (data.hora_notificacao) setWhatsappHora(data.hora_notificacao);
      })
      .catch(() => {/* silencia erros de rede — o default '08:00' permanece */});
  }, []);

  const saveWhatsappHora = async () => {
    setWhatsappHoraSaving(true);
    setWhatsappHoraError(null);
    try {
      const res = await fetch('/integracoes/whatsapp/horario', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-XSRF-TOKEN': decodeURIComponent(
            document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] ?? '',
          ),
        },
        body: JSON.stringify({ hora: whatsappHora }),
      });
      if (res.ok) {
        setWhatsappSheetOpen(false);
      } else {
        const err = await res.json().catch(() => ({}));
        setWhatsappHoraError(err?.message ?? 'Erro ao salvar horário.');
      }
    } catch {
      setWhatsappHoraError('Erro de conexão. Tente novamente.');
    } finally {
      setWhatsappHoraSaving(false);
    }
  };

  const handleDesktopToggle = async (checked: boolean) => {
    if (!isNotificationSupported()) return;

    if (!checked) {
      setDesktopEnabled(false);
      return;
    }

    const permission = await Notification.requestPermission();

    if (permission === 'granted') {
      setDesktopEnabled(true);
      new Notification('Notificações ativadas', {
        body: 'Você receberá alertas em tempo real neste computador.',
      });
    } else {
      setDesktopEnabled(false);
    }
  };

  const items: IChannelsItems = [
    {
      icon: Mail,
      title: 'Email',
      description: 'jamescollins@ktstudio.com',
      button: true,
      actions: <Switch id="channel-email" size="sm" defaultChecked />,
    },
    {
      icon: Phone,
      title: 'Mobile',
      description: '(225) 555-0118',
      button: true,
      actions: <Switch id="channel-mobile" size="sm" />,
    },
    {
      icon: FaWhatsapp,
      title: 'WhatsApp',
      description: `Alertas via WhatsApp · Horário: ${whatsappHora}`,
      actions: (
        <div className="flex items-center gap-1">
          <Button
            variant="ghost"
            size="icon"
            className="size-7 text-muted-foreground hover:text-foreground"
            onClick={() => setWhatsappSheetOpen(true)}
          >
            <SquarePen className="size-3.5" strokeWidth={1.5} />
          </Button>
          <Switch id="channel-whatsapp" size="sm" defaultChecked />
        </div>
      ),
    },
    {
      icon: Slack,
      title: 'Slack',
      description:
        'Receive instant alerts for messages and updates directly in Slack.',
      actions: (
        <Button variant="outline">
          <Link to="#">Connect Slack</Link>
        </Button>
      ),
    },
    {
      icon: Monitor,
      title: 'Desktop',
      description: isNotificationSupported()
        ? Notification.permission === 'denied'
          ? 'Permissão bloqueada. Habilite nas configurações do navegador.'
          : 'Receba alertas em tempo real neste computador.'
        : 'Seu navegador não suporta notificações desktop.',
      actions: (
        <Switch
          id="channel-desktop"
          size="sm"
          checked={desktopEnabled}
          disabled={
            !isNotificationSupported() || Notification.permission === 'denied'
          }
          onCheckedChange={handleDesktopToggle}
        />
      ),
    },
  ];

  const renderItem = (item: IChannelsItem, index: number) => {
    return (
      <CardNotification
        icon={item.icon}
        title={item.title}
        description={item.description}
        button={item.button}
        actions={item.actions}
        key={index}
      />
    );
  };

  return (
    <>
    <Sheet open={whatsappSheetOpen} onOpenChange={setWhatsappSheetOpen}>
      <SheetContent>
        <SheetHeader>
          <SheetTitle className="flex items-center gap-2">
            <FaWhatsapp className="size-4 text-green-500" />
            Horário de Notificação WhatsApp
          </SheetTitle>
        </SheetHeader>
        <div className="flex flex-col gap-4 py-6 px-1">
          <p className="text-sm text-muted-foreground">
            Defina o horário em que as notificações de contas a vencer serão enviadas via WhatsApp.
          </p>
          <FieldGroup>
            <Field>
              <Label htmlFor="whatsapp-hora">Horário de envio</Label>
              <InputGroup>
                <Input
                  id="whatsapp-hora"
                  type="time"
                  value={whatsappHora}
                  onChange={(e) => setWhatsappHora(e.target.value)}
                  className="appearance-none [&::-webkit-calendar-picker-indicator]:hidden [&::-webkit-calendar-picker-indicator]:appearance-none"
                />
                <InputAddon>
                  <Clock2Icon className="text-muted-foreground" />
                </InputAddon>
              </InputGroup>
            </Field>
          </FieldGroup>
        </div>
        <SheetFooter>
          {whatsappHoraError && (
            <p className="text-sm text-destructive w-full">{whatsappHoraError}</p>
          )}
          <Button variant="outline" onClick={() => setWhatsappSheetOpen(false)} disabled={whatsappHoraSaving}>
            Cancelar
          </Button>
          <Button
            onClick={saveWhatsappHora}
            disabled={whatsappHoraSaving}
          >
            {whatsappHoraSaving ? 'Salvando...' : 'Salvar horário'}
          </Button>
        </SheetFooter>
      </SheetContent>
    </Sheet>

    <Card>
      <CardHeader className="gap-2">
        <CardTitle>Canais de notificação</CardTitle>
        <div className="flex items-center gap-2">
          <Label htmlFor="channel-team-wide" className="text-sm">
            Alertas para toda a equipe
          </Label>
          <Switch id="channel-team-wide" size="sm" />
        </div>
      </CardHeader>
      <div id="notifications_cards">
        {items.map((item, index) => {
          return renderItem(item, index);
        })}
      </div>
    </Card>
    </>
  );
};

export { Channels, type IChannelsItem, type IChannelsItems };
