import { Fragment, useEffect, useState } from 'react';
import {
  Building2,
  EllipsisVertical,
  Mail,
  MapPin,
  MessagesSquare,
  Pencil,
  Users,
  Zap,
} from 'lucide-react';
import { Navbar, NavbarActions } from '@/components/layouts/layout-1/shared/navbar/navbar';
import { DropdownMenu9 } from '@/components/layouts/layout-1/shared/dropdown-menu/dropdown-menu-9';
import { UserHero } from '@/components/common/user-hero';
import { Container } from '@/components/common/container';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { toAbsoluteUrl } from '@/lib/helpers';
import { useAppData, type AppCompany } from '@/hooks/useAppData';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { CompanyEditSheet } from '@/pages/fraternidade/components/company-edit-sheet';
import { MassTimesTab } from '@/pages/fraternidade/components/mass-times-tab';

type EditableCompany = AppCompany & {
  details?: string | null;
  status?: 'active' | 'inactive' | string | null;
};

type FraternidadeTab = 'visao-geral' | 'horarios-missas' | 'configuracoes';

function PageMenu({ tab, onTabChange }: { tab: FraternidadeTab; onTabChange: (tab: FraternidadeTab) => void }) {
  return (
    <Tabs value={tab} onValueChange={(value) => onTabChange(value as FraternidadeTab)}>
      <TabsList variant="line" size="sm">
        <TabsTrigger value="visao-geral">Visão Geral</TabsTrigger>
        <TabsTrigger value="horarios-missas">Horários de Missas</TabsTrigger>
        <TabsTrigger value="configuracoes">Configurações</TabsTrigger>
      </TabsList>
    </Tabs>
  );
}

function ProfileCRMContent() {
  const { user, companyId, companies } = useAppData();
  const activeCompany = companies.find((company) => company.id === companyId) ?? null;

  return (
    <div className="grid grid-cols-3 gap-5">
      <Card className="col-span-2">
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Building2 className="size-4 text-primary" />
            {activeCompany?.name ?? 'Fraternidade não selecionada'}
          </CardTitle>
          <CardDescription>
            {activeCompany?.razao_social ?? 'Defina uma empresa ativa para visualizar os detalhes da fraternidade.'}
          </CardDescription>
        </CardHeader>
        <CardContent className="grid grid-cols-2 gap-4 text-sm">
          <div className="rounded-lg border border-border p-4">
            <p className="text-muted-foreground mb-1">CNPJ</p>
            <p className="font-medium">{activeCompany?.cnpj ?? 'Não informado'}</p>
          </div>
          <div className="rounded-lg border border-border p-4">
            <p className="text-muted-foreground mb-1">Empresa ativa</p>
            <Badge variant="primary" appearance="light" size="sm">
              {activeCompany ? 'Ativa' : 'Indefinida'}
            </Badge>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle className="text-center w-full">Contato</CardTitle>
          <CardDescription className="text-center w-full">Informações do usuário logado</CardDescription>
        </CardHeader>
        <CardContent className="space-y-3 text-sm">
          <div className="flex items-center justify-center gap-2 text-center">
            <Users className="size-4 text-muted-foreground mt-0.5" />
            <span>{user.name}</span>
          </div>
          <div className="flex items-center justify-center gap-2 text-center">
            <Mail className="size-4 text-muted-foreground mt-0.5" />
            <span className="break-all">{user.email}</span>
          </div>
          <div className="flex items-center justify-center gap-2 text-center text-muted-foreground">
            <MapPin className="size-4 mt-0.5" />
            <span>Dados administrativos da fraternidade</span>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}

export function FraternidadePage() {
  const { user, companyId, companies } = useAppData();
  const activeCompany = companies.find((company) => company.id === companyId) ?? null;
  const [sheetOpen, setSheetOpen] = useState(false);
  const [activeTab, setActiveTab] = useState<FraternidadeTab>('visao-geral');
  const [companyView, setCompanyView] = useState<EditableCompany | null>(activeCompany);

  useEffect(() => {
    setCompanyView(activeCompany);
  }, [activeCompany]);

  const currentCompany = companyView ?? activeCompany;
  const fixedPageClass = 'w-[1220px] min-w-[1220px] max-w-[1220px]';
  const companyEmail = currentCompany?.email ?? user.email;
  const cnpjLabel = currentCompany?.cnpj ?? 'CNPJ não informado';
  const addressParts = [
    currentCompany?.address?.rua,
    currentCompany?.address?.numero,
    currentCompany?.address?.bairro,
    currentCompany?.address?.cidade,
    currentCompany?.address?.uf,
  ].filter(Boolean);
  const enderecoLabel = addressParts.length > 0 ? addressParts.join(', ') : 'Endereço não informado';
  const emailAndAddress = `${companyEmail} • ${enderecoLabel}`;

  const image = currentCompany?.avatar_url ? (
    <img
      src={currentCompany.avatar_url}
      className="rounded-full border-3 border-green-500 size-[100px] shrink-0 object-cover"
      alt={currentCompany.name}
    />
  ) : (
    <img
      src={toAbsoluteUrl('/media/avatars/300-1.png')}
      className="rounded-full border-3 border-green-500 size-[100px] shrink-0"
      alt="image"
    />
  );

  return (
    <Fragment>
      <UserHero
        name={currentCompany?.name ?? user.name}
        image={image}
        containerClassName={fixedPageClass}
        info={[
          {
            label: currentCompany?.razao_social ?? 'Fraternidade',
            icon: Zap,
            className: 'basis-full justify-center text-center',
          },
          {
            label: cnpjLabel,
            icon: MapPin,
            className: 'basis-full justify-center text-center',
          },
          {
            label: emailAndAddress,
            icon: Mail,
            className: 'basis-full justify-center text-center text-xs font-normal text-muted-foreground',
          },
        ]}
      />
      <Container className={fixedPageClass}>
        <Navbar>
          <PageMenu tab={activeTab} onTabChange={setActiveTab} />
          <NavbarActions>
            <Button variant="outline" onClick={() => setSheetOpen(true)}>
              <Pencil /> Editar
            </Button>
            <Button onClick={() => setActiveTab('horarios-missas')}>
              <Users /> Horários de Missas
            </Button>
            <Button variant="outline" mode="icon">
              <MessagesSquare />
            </Button>
            <DropdownMenu9
              trigger={(
                <Button variant="outline" mode="icon">
                  <EllipsisVertical />
                </Button>
              )}
            />
          </NavbarActions>
        </Navbar>
      </Container>
      <Container className={fixedPageClass}>
        {activeTab === 'horarios-missas' ? (
          <MassTimesTab />
        ) : activeTab === 'configuracoes' ? (
          <div className="flex items-center justify-center py-20 text-muted-foreground text-sm">
            Configurações em breve.
          </div>
        ) : (
          <ProfileCRMContent />
        )}
      </Container>

      <CompanyEditSheet
        open={sheetOpen}
        onOpenChange={setSheetOpen}
        onSaved={(updated) => setCompanyView(updated)}
      />
    </Fragment>
  );
}