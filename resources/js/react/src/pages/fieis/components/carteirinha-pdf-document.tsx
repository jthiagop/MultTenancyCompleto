/**
 * carteirinha-pdf-document.tsx
 *
 * Geração de PDF da carteirinha do dizimista 100% client-side.
 *
 * Dependências: @react-pdf/renderer, qrcode, bwip-js
 *
 * Exporta:
 *  - buildCarteirinhaImages(payload)     gera QR (PNG) e barcode (PNG) no browser
 *  - CarteirinhaSideBySideDoc(props)     PDF tamanho exato (2×CR80 + margens) — frente | verso lado a lado
 *  - CarteirinhaDuplexDoc(props)         PDF CR80 × 2 págs (tamanho exato) — frente / verso
 */

import {
  Document,
  Image,
  Page,
  StyleSheet,
  Text,
  View,
} from '@react-pdf/renderer';
import QRCode from 'qrcode';
import bwipjs from 'bwip-js';

// ── Tamanho fixo da carteirinha ──────────────────────────────────────────────
// Padrão CR80 (ISO/IEC 7810 ID-1): 85.6 mm × 54 mm em pontos PDF (1 mm = 2.835 pt).
// Frente e verso sempre terão exatamente este tamanho.
const CARD_W = 243; // ≈ 85.8 mm
const CARD_H = 153; // ≈ 54.0 mm
const CARD_PAD = 14; // margem ao redor do cartão nas páginas com padding (pt)
const CARD_GAP = 8;  // espaço entre frente e verso no layout lado a lado (pt)

// ── Tipos ───────────────────────────────────────────────────────────────────

export type CarteirinhaData = {
  codigo: string;
  qr_payload: string;
  fiel: {
    id: number;
    nome_completo: string;
    avatar_url: string | null;
  };
  company?: {
    nome: string | null;
    logo_url: string | null;
  };
};

export type CarteirinhaImages = {
  qrDataUrl: string;
  barcodeDataUrl: string;
};

// ── Placeholder 1×1 px transparente (evita crash do <Image> quando src=null) ─

const TRANSPARENT_PNG =
  'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';

// ── Geração de imagens client-side ───────────────────────────────────────────

/**
 * Gera as imagens do QR Code e do barcode Code128 no browser usando qrcode e
 * bwip-js. Retorna data URLs PNG prontos para uso com <Image> do @react-pdf.
 */
export async function buildCarteirinhaImages(
  payload: CarteirinhaData,
): Promise<CarteirinhaImages> {
  // QR Code → PNG data URL
  const qrDataUrl = await QRCode.toDataURL(payload.qr_payload, {
    errorCorrectionLevel: 'M',
    margin: 1,
    width: 200,
    color: { dark: '#000000', light: '#FFFFFF' },
  });

  // Code128 → canvas → PNG data URL
  const canvas = document.createElement('canvas');
  bwipjs.toCanvas(canvas, {
    bcid: 'code128',
    text: payload.codigo,
    scale: 3,
    height: 12,
    includetext: false,
    backgroundcolor: 'FFFFFF',
  });
  const barcodeDataUrl = canvas.toDataURL('image/png');

  return { qrDataUrl, barcodeDataUrl };
}

// ── Helpers de imagem (carrega URL para data URI via fetch) ──────────────────

/**
 * Busca a imagem de uma URL (mesma origem = sem CORS) e converte para data
 * URL. Retorna o placeholder transparente em caso de falha para não quebrar
 * o layout do PDF.
 */
async function fetchImageAsDataUrl(url: string | null | undefined): Promise<string> {
  if (!url) return TRANSPARENT_PNG;
  try {
    const res = await fetch(url, { credentials: 'same-origin' });
    if (!res.ok) return TRANSPARENT_PNG;
    const blob = await res.blob();
    return await new Promise<string>((resolve) => {
      const reader = new FileReader();
      reader.onload = () => resolve(reader.result as string);
      reader.onerror = () => resolve(TRANSPARENT_PNG);
      reader.readAsDataURL(blob);
    });
  } catch {
    return TRANSPARENT_PNG;
  }
}

/**
 * Pré-carrega avatar e logo como data URLs. Deve ser chamado uma vez após
 * `buildCarteirinhaImages` para que o @react-pdf consiga embutir as imagens
 * sem problemas de CORS ou content-type inesperado.
 */
export async function buildCarteirinhaExternalImages(data: CarteirinhaData): Promise<{
  avatarDataUrl: string;
  logoDataUrl: string;
}> {
  const [avatarDataUrl, logoDataUrl] = await Promise.all([
    fetchImageAsDataUrl(data.fiel.avatar_url),
    fetchImageAsDataUrl(data.company?.logo_url),
  ]);
  return { avatarDataUrl, logoDataUrl };
}

// ── Meses ────────────────────────────────────────────────────────────────────

const MESES = [
  'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
  'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro',
];

// ── Estilos compartilhados ───────────────────────────────────────────────────

const C = {
  dark: '#18181b',
  light: '#fafafa',
  border: '#d4d4d8',
  muted: '#71717a',
  bg: '#f9fafb',
  bgAlt: '#f4f4f5',
};

const shared = StyleSheet.create({
  // Frente
  card: {
    width: CARD_W,
    height: CARD_H,
    flexDirection: 'column',
    border: `1px solid ${C.border}`,
    borderRadius: 6,
    overflow: 'hidden',
    backgroundColor: '#ffffff',
  },
  header: {
    backgroundColor: C.dark,
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 10,
    paddingVertical: 6,
  },
  headerTitulo: { flexDirection: 'column' },
  headerEyebrow: {
    fontSize: 7,
    color: C.light,
    textTransform: 'uppercase',
    letterSpacing: 0.5,
    opacity: 0.7,
  },
  headerOrganismo: { fontSize: 11, color: C.light, fontWeight: 'bold' },
  headerCodigo: {
    fontSize: 12,
    color: C.light,
    fontWeight: 'bold',
    letterSpacing: 0.4,
    fontFamily: 'Courier',
  },
  body: {
    flex: 1,
    flexDirection: 'row',
    gap: 8,
    padding: 8,
    alignItems: 'center',
  },
  fotoBox: {
    width: 70,
    height: 70,
    border: `1px solid ${C.border}`,
    borderRadius: 4,
    backgroundColor: C.bgAlt,
    overflow: 'hidden',
  },
  fotoImg: { width: '100%', height: '100%', objectFit: 'cover' },
  logoImg: { width: '100%', height: '100%', objectFit: 'contain', padding: 4 },
  info: { flex: 1, flexDirection: 'column', gap: 3 },
  infoEyebrow: { fontSize: 7, color: C.muted, textTransform: 'uppercase', letterSpacing: 0.5 },
  infoNome: { fontSize: 11, fontWeight: 'bold', color: C.dark },
  codes: { flexDirection: 'row', alignItems: 'flex-end', gap: 5, marginTop: 2 },
  qrBox: {
    width: 54,
    height: 54,
    border: `1px solid ${C.border}`,
    borderRadius: 3,
    padding: 2,
    backgroundColor: '#ffffff',
  },
  qrImg: { width: '100%', height: '100%' },
  barcodeWrap: { flex: 1, flexDirection: 'column', gap: 2 },
  barcodeBox: {
    border: `1px solid ${C.border}`,
    borderRadius: 3,
    padding: 2,
    backgroundColor: '#ffffff',
  },
  barcodeImg: { width: '100%', height: 30 },
  barcodeMeta: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    fontSize: 7,
    color: C.muted,
  },
  barcodeMetaCodigo: { fontFamily: 'Courier', color: C.dark, fontWeight: 'bold' },
  footer: {
    borderTop: `1px solid ${C.border}`,
    backgroundColor: C.bg,
    paddingHorizontal: 8,
    paddingVertical: 3,
    textAlign: 'center',
  },
  footerText: { fontSize: 7, color: C.muted },

  // Verso
  versoHeader: {
    backgroundColor: C.dark,
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 10,
    paddingVertical: 6,
  },
  versoOrganismo: {
    fontSize: 10,
    color: C.light,
    fontWeight: 'bold',
    textTransform: 'uppercase',
    letterSpacing: 0.5,
  },
  versoCodigo: {
    fontSize: 12,
    color: C.light,
    fontWeight: 'bold',
    fontFamily: 'Courier',
  },
  controle: {
    flex: 1,
    flexDirection: 'row',
    gap: 8,
    padding: 8,
  },
  tabela: { flex: 1, flexDirection: 'column' },
  tabelaHeader: {
    flexDirection: 'row',
    backgroundColor: C.bgAlt,
    borderBottom: `1px solid ${C.border}`,
  },
  tabelaRow: { flexDirection: 'row', borderBottom: `1px solid ${C.border}` },
  thMes: {
    flex: 3,
    fontSize: 7,
    color: C.muted,
    textTransform: 'uppercase',
    letterSpacing: 0.4,
    fontWeight: 'bold',
    paddingHorizontal: 3,
    paddingVertical: 2,
    borderRight: `1px solid ${C.border}`,
  },
  thCheck: {
    width: 14,
    fontSize: 7,
    color: C.muted,
    textTransform: 'uppercase',
    letterSpacing: 0.4,
    fontWeight: 'bold',
    paddingHorizontal: 2,
    paddingVertical: 2,
    textAlign: 'center',
    borderRight: `1px solid ${C.border}`,
  },
  thData: {
    flex: 2.5,
    fontSize: 7,
    color: C.muted,
    textTransform: 'uppercase',
    letterSpacing: 0.4,
    fontWeight: 'bold',
    paddingHorizontal: 3,
    paddingVertical: 2,
    borderRight: `1px solid ${C.border}`,
  },
  thValor: {
    flex: 2.5,
    fontSize: 7,
    color: C.muted,
    textTransform: 'uppercase',
    letterSpacing: 0.4,
    fontWeight: 'bold',
    paddingHorizontal: 3,
    paddingVertical: 2,
    textAlign: 'right',
  },
  tdMes: {
    flex: 3,
    fontSize: 8,
    fontWeight: 'bold',
    color: C.dark,
    paddingHorizontal: 3,
    paddingVertical: 2,
    borderRight: `1px solid ${C.border}`,
  },
  tdCheck: {
    width: 14,
    paddingHorizontal: 2,
    paddingVertical: 2,
    textAlign: 'center',
    borderRight: `1px solid ${C.border}`,
  },
  checkBox: {
    width: 8,
    height: 8,
    border: `1px solid ${C.muted}`,
    margin: 'auto',
  },
  tdData: {
    flex: 2.5,
    paddingHorizontal: 3,
    paddingVertical: 2,
    borderRight: `1px solid ${C.border}`,
    height: 14,
  },
  tdValor: {
    flex: 2.5,
    paddingHorizontal: 3,
    paddingVertical: 2,
    textAlign: 'right',
    height: 14,
  },
});

// ── Componentes internos ─────────────────────────────────────────────────────

type FrenteProps = {
  data: CarteirinhaData;
  images: CarteirinhaImages;
  avatarDataUrl: string;
  logoDataUrl: string;
};

function CarteirinhaFrentePdf({ data, images, avatarDataUrl, logoDataUrl }: FrenteProps) {
  const organismo = data.company?.nome ?? 'Igreja';

  return (
    <View style={shared.card}>
      <View style={shared.header}>
        <View style={shared.headerTitulo}>
          <Text style={shared.headerEyebrow}>Carteirinha do Dizimista</Text>
          <Text style={shared.headerOrganismo}>{organismo}</Text>
        </View>
        <Text style={shared.headerCodigo}>{data.codigo}</Text>
      </View>

      <View style={shared.body}>
        {/* Foto do fiel */}
        <View style={shared.fotoBox}>
          <Image src={avatarDataUrl} style={shared.fotoImg} />
        </View>

        {/* Centro: nome + QR + barcode */}
        <View style={shared.info}>
          <Text style={shared.infoEyebrow}>Dizimista</Text>
          <Text style={shared.infoNome} numberOfLines={2}>{data.fiel.nome_completo}</Text>

          <View style={shared.codes}>
            <View style={shared.qrBox}>
              <Image src={images.qrDataUrl} style={shared.qrImg} />
            </View>
            <View style={shared.barcodeWrap}>
              <View style={shared.barcodeBox}>
                <Image src={images.barcodeDataUrl} style={shared.barcodeImg} />
              </View>
              <View style={shared.barcodeMeta}>
                <Text>Code128</Text>
                <Text style={shared.barcodeMetaCodigo}>{data.codigo}</Text>
              </View>
            </View>
          </View>
        </View>

        {/* Logo da company */}
        <View style={shared.fotoBox}>
          <Image src={logoDataUrl} style={shared.logoImg} />
        </View>
      </View>

      <View style={shared.footer}>
        <Text style={shared.footerText}>
          Apresente esta carteirinha ao realizar a contribuição do dízimo.
        </Text>
      </View>
    </View>
  );
}

function CarteirinhaVersoPdf({ ano }: { ano: number }) {
  const colunaA = MESES.slice(0, 6);
  const colunaB = MESES.slice(6, 12);

  const TabelaMeses = ({ meses }: { meses: string[] }) => (
    <View style={shared.tabela}>
      <View style={shared.tabelaHeader}>
        <Text style={shared.thMes}>Mês</Text>
        <Text style={shared.thCheck}>✓</Text>
        <Text style={shared.thData}>Data</Text>
        <Text style={shared.thValor}>Valor</Text>
      </View>
      {meses.map((mes) => (
        <View key={mes} style={shared.tabelaRow}>
          <Text style={shared.tdMes}>{mes}</Text>
          <View style={shared.tdCheck}>
            <View style={shared.checkBox} />
          </View>
          <View style={shared.tdData} />
          <View style={shared.tdValor} />
        </View>
      ))}
    </View>
  );

  return (
    <View style={shared.card}>
      <View style={shared.versoHeader}>
        <Text style={shared.versoOrganismo}>Controle de Dízimos</Text>
        <Text style={shared.versoCodigo}>{ano}</Text>
      </View>

      <View style={shared.controle}>
        <TabelaMeses meses={colunaA} />
        <TabelaMeses meses={colunaB} />
      </View>

      <View style={shared.footer}>
        <Text style={shared.footerText}>
          Marque o mês contribuído, anote a data do pagamento e o valor.
        </Text>
      </View>
    </View>
  );
}

// ── Documentos exportados ────────────────────────────────────────────────────

export type CarteirinhaPdfProps = {
  data: CarteirinhaData;
  images: CarteirinhaImages;
  avatarDataUrl: string;
  logoDataUrl: string;
};

/**
 * PDF com frente e verso lado a lado em 1 folha.
 * Tamanho da página calculado para acomodar exatamente 2 cartões CR80
 * (CARD_W × CARD_H) com margens iguais. Ideal para cortar e entregar.
 */
export function CarteirinhaSideBySideDoc({
  data,
  images,
  avatarDataUrl,
  logoDataUrl,
}: CarteirinhaPdfProps) {
  const ano = new Date().getFullYear();
  // Página exata: 2 cartões + 2 gaps (entre frente/sep e sep/verso) + sep + padding
  const SEP_W = 1;
  const pageW = CARD_W * 2 + CARD_GAP * 2 + SEP_W + CARD_PAD * 2;
  const pageH = CARD_H + CARD_PAD * 2;

  return (
    <Document
      title={`Carteirinha — ${data.fiel.nome_completo}`}
      author="Dominus Sistema"
    >
      <Page
        size={[pageW, pageH]}
        style={{
          flexDirection: 'row',
          gap: CARD_GAP,
          padding: CARD_PAD,
          backgroundColor: '#ffffff',
          alignItems: 'center',
        }}
      >
        <CarteirinhaFrentePdf
          data={data}
          images={images}
          avatarDataUrl={avatarDataUrl}
          logoDataUrl={logoDataUrl}
        />
        {/* Linha de corte */}
        <View style={{ width: 0.5, height: CARD_H, backgroundColor: '#a1a1aa', opacity: 0.4 }} />
        <CarteirinhaVersoPdf ano={ano} />
      </Page>
    </Document>
  );
}

/**
 * PDF frente/verso — 2 páginas do tamanho exato da carteirinha (CR80).
 * Cada página tem exatamente CARD_W × CARD_H pts — sem margens extras.
 * Ideal para impressão duplex: a impressora coloca frente em uma face e
 * verso na outra, e o cartão é recortado no tamanho certo.
 */
export function CarteirinhaDuplexDoc({
  data,
  images,
  avatarDataUrl,
  logoDataUrl,
}: CarteirinhaPdfProps) {
  const ano = new Date().getFullYear();

  return (
    <Document
      title={`Carteirinha — ${data.fiel.nome_completo}`}
      author="Dominus Sistema"
    >
      {/* Página 1: Frente — tamanho exato da carteirinha */}
      <Page size={[CARD_W, CARD_H]} style={{ backgroundColor: '#ffffff' }}>
        <CarteirinhaFrentePdf
          data={data}
          images={images}
          avatarDataUrl={avatarDataUrl}
          logoDataUrl={logoDataUrl}
        />
      </Page>

      {/* Página 2: Verso — mesmo tamanho */}
      <Page size={[CARD_W, CARD_H]} style={{ backgroundColor: '#ffffff' }}>
        <CarteirinhaVersoPdf ano={ano} />
      </Page>
    </Document>
  );
}
