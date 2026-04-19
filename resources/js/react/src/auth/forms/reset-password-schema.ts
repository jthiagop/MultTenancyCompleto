import { z } from 'zod';

export const getResetRequestSchema = () => {
  return z.object({
    email: z
      .string()
      .min(1, { message: 'Informe o e-mail.' })
      .email({ message: 'E-mail inválido.' }),
  });
};

export const getNewPasswordSchema = () => {
  return z
    .object({
      password: z.string().min(8, { message: 'A senha deve ter pelo menos 8 caracteres.' }),
      confirmPassword: z.string().min(1, { message: 'Confirme a senha.' }),
    })
    .refine((data) => data.password === data.confirmPassword, {
      message: 'As senhas não conferem.',
      path: ['confirmPassword'],
    });
};

export type ResetRequestSchemaType = z.infer<ReturnType<typeof getResetRequestSchema>>;
export type NewPasswordSchemaType = z.infer<ReturnType<typeof getNewPasswordSchema>>;
