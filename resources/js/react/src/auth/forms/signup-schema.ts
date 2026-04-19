import { z } from 'zod';

export const getSignupSchema = () => {
  return z
    .object({
      email: z
        .string()
        .min(1, { message: 'Informe o e-mail.' })
        .email({ message: 'E-mail inválido.' }),
      password: z.string().min(8, { message: 'A senha deve ter pelo menos 8 caracteres.' }),
      confirmPassword: z.string().min(1, { message: 'Confirme a senha.' }),
      firstName: z.string().min(1, { message: 'Informe o nome.' }),
      lastName: z.string().min(1, { message: 'Informe o sobrenome.' }),
      terms: z.boolean().refine((val) => val === true, {
        message: 'Você precisa aceitar os termos.',
      }),
    })
    .refine((data) => data.password === data.confirmPassword, {
      message: 'As senhas não conferem.',
      path: ['confirmPassword'],
    });
};

export type SignupSchemaType = z.infer<ReturnType<typeof getSignupSchema>>;
